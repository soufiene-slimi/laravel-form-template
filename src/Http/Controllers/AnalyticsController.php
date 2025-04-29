<?php

namespace SoufieneSlimi\LaravelFormTemplate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SoufieneSlimi\LaravelFormTemplate\Models\Template;
use SoufieneSlimi\LaravelFormTemplate\Models\TemplateUsage;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filters from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $templateId = $request->input('template_id');
        $userId = $request->input('user_id');
        
        // Get all templates for the filter dropdown
        $templates = Template::all();
        
        // Get filtered usages
        $usages = TemplateUsage::with('template')
            ->dateRange($startDate, $endDate)
            ->byTemplate($templateId)
            ->byUser($userId)
            ->latest()
            ->paginate(15);
        
        // Get usage statistics
        $totalUsages = TemplateUsage::count();
        
        // Get top templates by usage
        $topTemplates = TemplateUsage::select('template_id', DB::raw('count(*) as total'))
            ->groupBy('template_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('template')
            ->get();
        
        // Get usage over time (last 30 days)
        $usageOverTime = TemplateUsage::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('laravel-form-template::analytics.index', compact(
            'templates',
            'usages',
            'totalUsages',
            'topTemplates',
            'usageOverTime',
            'startDate',
            'endDate',
            'templateId',
            'userId'
        ));
    }
    
    /**
     * Display detailed analytics for a specific template.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        
        // Get filters from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Get filtered usages for this template
        $usages = $template->usages()
            ->dateRange($startDate, $endDate)
            ->latest()
            ->paginate(15);
        
        // Get usage over time for this template (last 30 days)
        $usageOverTime = $template->usages()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get unique users who used this template
        $uniqueUsers = $template->usages()
            ->select('user_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->count();
        
        return view('laravel-form-template::analytics.show', compact(
            'template',
            'usages',
            'usageOverTime',
            'uniqueUsers',
            'startDate',
            'endDate'
        ));
    }
}

