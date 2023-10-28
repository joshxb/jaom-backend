<?php

namespace App\Response\Manager\api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServerManagerResponse
{
    public function getServerInfo()
    {
        $user = Auth::user();

        if ($user->type !== 'admin') {
            return response()->json([
                'message' => 'You are not authorized to perform this action. Admin access required.'
            ], 403);
        }

        // Get the database connection configuration from .env
        $dbConnection = env('DB_CONNECTION', 'mysql'); // Default to mysql if .env is empty

        // Query to retrieve the database engine
        $engineResult = DB::table('information_schema.tables')
            ->where('table_schema', env('DB_DATABASE'))
            ->select('ENGINE')
            ->first();

        $tableStatus = DB::table('information_schema.tables')
            ->select('table_name', 'engine', 'data_length', 'index_length')
            ->where('table_schema', env('DB_DATABASE'))
            ->get();

        // Calculate the number of rows for data_length and index_length
        $dataRowCount = DB::table('information_schema.tables')
            ->where('table_schema', env('DB_DATABASE'))
            ->sum('data_length');

        $indexRowCount = DB::table('information_schema.tables')
            ->where('table_schema', env('DB_DATABASE'))
            ->sum('index_length');


        // Get CPU Usage
        $cpuUsageResult = DB::select('SHOW GLOBAL STATUS LIKE "CPU%"');
        $cpuUsage = null;

        foreach ($cpuUsageResult as $row) {
            if ($row->Variable_name === 'CPU_USAGE') {
                $cpuUsage = $row->Value;
                break;
            }
        }

        $cpuUsage = $cpuUsage !== null ? $cpuUsage . '%' : 'N/A';

        // Get Network Traffic
        $networkTrafficResult = DB::select('SHOW STATUS LIKE "Bytes_received"');
        $rxBytes = null;

        foreach ($networkTrafficResult as $row) {
            if ($row->Variable_name === 'Bytes_received') {
                $rxBytes = $row->Value;
                break;
            }
        }

        $networkTraffic = [
            'rx_bytes' => $rxBytes !== null ? $rxBytes : 'N/A',
            'tx_bytes' => 'N/A',
            // You can similarly query "Bytes_sent" for outgoing traffic.
        ];

        // Memory Usage (alternative method)
        $memoryUsage = round(memory_get_usage(true) / (1024 * 1024), 2); // in MB

        // Display the server information with labels in MB and KB
        return [
            'databaseType' => $dbConnection,
            'databaseEngine' => $engineResult,
            'totalStorage' => (($dataRowCount + $indexRowCount) / 1024) / 1024 . ' MB',
            'tableCount' => count($tableStatus),
            'tableStatus' => $tableStatus,
            'cpuUsage' => $cpuUsage,
            'memoryUsage' => $memoryUsage . ' MB',
            'networkTraffic' => $networkTraffic,
        ];
    }
}
