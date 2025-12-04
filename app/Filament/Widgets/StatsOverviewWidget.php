<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\BugReport;
use App\Models\Article;
use App\Models\ChatConversation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Contact Messages Stats
        $totalContactMessages = ContactMessage::count();
        $unreadContactMessages = ContactMessage::where('is_reviewed', false)->count();
        
        // Bug Reports Stats
        $totalBugReports = BugReport::count();
        $pendingBugReports = BugReport::where('status', 'pending')->count();
        $resolvedBugReports = BugReport::where('status', 'resolved')->count();
        
        // Articles Stats
        $totalArticles = Article::count();
        $publishedArticles = Article::whereNotNull('published_at')->count();
        
        // Chat Conversations Stats
        $totalChatConversations = ChatConversation::count();
        $activeChatConversations = ChatConversation::whereHas('messages', function($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->count();

        return [
            Stat::make('Pesan Kontak', $totalContactMessages)
                ->description($unreadContactMessages > 0 ? "{$unreadContactMessages} belum dibaca" : 'Semua sudah dibaca')
                ->descriptionIcon($unreadContactMessages > 0 ? 'heroicon-m-envelope' : 'heroicon-m-check-circle')
                ->color($unreadContactMessages > 0 ? 'warning' : 'success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            
            Stat::make('Laporan Bug', $totalBugReports)
                ->description("{$pendingBugReports} pending, {$resolvedBugReports} resolved")
                ->descriptionIcon($pendingBugReports > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->color($pendingBugReports > 0 ? 'danger' : 'success')
                ->chart([3, 2, 5, 4, 6, 7, 4, 3]),
            
            Stat::make('Artikel', $totalArticles)
                ->description("{$publishedArticles} dipublikasikan")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([2, 4, 3, 5, 6, 4, 7, 6]),
            
            Stat::make('Percakapan Chat', $totalChatConversations)
                ->description("{$activeChatConversations} aktif minggu ini")
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->chart([4, 6, 5, 7, 8, 6, 9, 7]),
        ];
    }
}
