<?php

use App\Http\Controllers\NotificationController;

Route::post('/notifications/send', [NotificationController::class, 'send']);
Route::get('/notifications/{userId}', [NotificationController::class, 'getAll']);
Route::get('/notifications/{userId}/unread', [NotificationController::class, 'getUnread']);
Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);



?>