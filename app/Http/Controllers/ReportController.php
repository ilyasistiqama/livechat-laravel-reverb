<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Tanggal yang dipilih, default ke hari ini
        $date = $request->input('date', date('Y-m-d'));

        // 1. Laporan berapa banyak chat yg berakhir pertanggal dipilih
        // Menggunakan distinct room_code karena satu room terdiri dari banyak message
        $finishedChatsCount = Chat::where('finished', true)
            ->whereDate('updated_at', $date)
            ->distinct('room_code')
            ->count('room_code');

        // 2. Laporan rata-rata respon saling balas chat antara member dan admin per room
        // Ambil room_code yang aktif (ada pesan masuk) pada tanggal tersebut
        $roomCodes = Chat::whereDate('created_at', $date)
            ->where('type', 'customer-to-admin')
            ->distinct()
            ->pluck('room_code');

        $chats = Chat::whereIn('room_code', $roomCodes)
            ->orderBy('room_code')
            ->orderBy('created_at')
            ->get();

        $roomStats = [];
        $globalAdminResponseTotal = 0;
        $globalAdminResponseCount = 0;

        foreach ($chats->groupBy('room_code') as $roomCode => $roomChats) {
            $adminResponseTotal = 0;
            $adminResponseCount = 0;

            $memberResponseTotal = 0;
            $memberResponseCount = 0;

            $lastSenderType = null;
            $lastMessageTime = null;

            foreach ($roomChats as $chat) {
                if ($lastSenderType !== null && $lastSenderType !== $chat->from_type) {
                    $responseTime = abs($chat->created_at->diffInSeconds($lastMessageTime));
                    
                    if ($chat->from_type === 'admin') {
                        $adminResponseTotal += $responseTime;
                        $adminResponseCount++;
                    } elseif ($chat->from_type === 'member') {
                        $memberResponseTotal += $responseTime;
                        $memberResponseCount++;
                    }
                }
                $lastSenderType = $chat->from_type;
                $lastMessageTime = $chat->created_at;
            }

            $avgAdmin = $adminResponseCount > 0 ? $adminResponseTotal / $adminResponseCount : 0;
            $avgMember = $memberResponseCount > 0 ? $memberResponseTotal / $memberResponseCount : 0;
            
            $overallResponseTotal = $adminResponseTotal + $memberResponseTotal;
            $overallResponseCount = $adminResponseCount + $memberResponseCount;
            $avgOverall = $overallResponseCount > 0 ? $overallResponseTotal / $overallResponseCount : 0;
            
            $globalAdminResponseTotal += $adminResponseTotal;
            $globalAdminResponseCount += $adminResponseCount;

              
            $roomStats[] = (object) [
                'room_code' => $roomCode,
                'total_chat' => $roomChats->count(),
                'avg_admin' => $avgAdmin,
                'avg_member' => $avgMember,
                'avg_overall' => $avgOverall,
                'avg_admin_text' => $this->formatTime($avgAdmin),
                'avg_member_text' => $this->formatTime($avgMember),
                'avg_overall_text' => $this->formatTime($avgOverall),
            ];
        }

        $globalAvg = $globalAdminResponseCount > 0 ? $globalAdminResponseTotal / $globalAdminResponseCount : 0;
        $globalAvgText = $this->formatTime($globalAvg);

        return view('admin.report', compact('date', 'finishedChatsCount', 'roomStats', 'globalAvgText'));
    }

    private function formatTime($seconds)
    {
        if ($seconds == 0) return '-';
        
        $seconds = abs($seconds);
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds % 3600) / 60);
        $secs = round($seconds % 60);
        
        $text = "";
        if ($hours > 0) {
            $text .= "{$hours} Jam ";
        }
        if ($mins > 0) {
            $text .= "{$mins} Menit ";
        }
        if ($secs > 0 || $text === "") {
            $text .= "{$secs} Detik";
        }
        return trim($text);
    }
}
