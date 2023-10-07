<?php

namespace App\Response\Manager\api;

use App\Models\PageAnalytics;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PageAnalyticsManagerResponse
{
    public static $paginate = 10;

    public function index()
    {
        $user = Auth::user();

        $month = request()->input('month');
        $year = request()->input('year');

        if ($user->type == 'admin') {
            $query = PageAnalytics::orderByDesc('created_at');
            $data = $query->paginate(self::$paginate);

            $totalVisits = PageAnalytics::count();

            if ($month && $year) {
                $query->whereYear('created_at', $year)->whereMonth('created_at', \Carbon\Carbon::parse($month)->month);
            }

            $totalVisitsPerMonthAndYear = $query->count();

            $results = DB::table('page_analytics')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as visits'))
                ->whereYear('created_at', $year)->whereMonth('created_at', \Carbon\Carbon::parse($month)->month)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            $pageVisits = [];
            foreach ($results as $result) {
                $pageVisits[] = [
                    'date' => $result->date,
                    'visits' => intval($result->visits),
                ];
            }

            $responseData = [
                'data' => $data->items(),
                'total_visits' => $totalVisits,
                'total_visits_per_month_and_year' => $totalVisitsPerMonthAndYear,
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => self::$paginate,
                'page_visits' => $pageVisits
            ];

            return response()->json($responseData);
        } else {
            return response()->json(['message' => 'Permission denied'], 401);
        }
    }

    public function show($id)
    {
        $user = Auth::user();

        try {
            if ($user->type == 'admin') {
                $analytics = PageAnalytics::findOrFail($id);
                return response()->json([
                    'data' => $analytics
                ], 200);
            } else {
                return response()->json(['message' => 'Permission denied'], 401);
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Analytics not found'], 404);
        }
    }

    public function store()
    {
        request()->validate([
            'session_id' => 'required|string',
            'userAgent' => 'required|string',
            'device' => 'required|string',
            'browser' => 'required|string',
            'os' => 'required|string',
            'os_version' => 'required|string',
            'browser_version' => 'required|string',
            'deviceType' => 'required|string',
            'orientation' => 'required|string'
        ]);

        $existingPageVisit = PageAnalytics::where([
            ['session_id', '=', request()->get('session_id')],
            ['userAgent', '=', request()->get('userAgent')],
            ['browser', '=', request()->get('browser')],
            ['browser_version', '=', request()->get('browser_version')],
        ])->first();

        $data = null;

        if (!$existingPageVisit) {
            $data = PageAnalytics::create([
                'session_id' => request()->get('session_id'),
                'userAgent' => request()->get('userAgent'),
                'device' => request()->get('device'),
                'browser' => request()->get('browser'),
                'os' => request()->get('os'),
                'os_version' => request()->get('os_version'),
                'browser_version' => request()->get('browser_version'),
                'deviceType' => request()->get('deviceType'),
                'orientation' => request()->get('orientation')
            ]);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Analytics stored successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        try {
            if ($user->type == 'admin') {
                $analytics = PageAnalytics::findOrFail($id);

                $analytics->delete();

                return response()->json(['message' => 'Analytics deleted successfully']);
            } else {
                return response()->json(['message' => 'Permission denied'], 401);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Analytics not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }
    }

    public function destroyAll()
    {
        $user = Auth::user();

        try {
            if ($user->type == 'admin') {
                PageAnalytics::truncate();

                return response()->json(['message' => 'All analytics deleted successfully']);
            } else {
                return response()->json(['message' => 'Permission denied'], 401);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Analytics not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }
    }
}

