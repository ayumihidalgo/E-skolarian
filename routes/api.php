<?php

use App\Http\Controllers\NotificationController;

// Route to send a notification
Route::post('/notifications/send', [NotificationController::class, 'send']);

// Route to get all notifications for a specific user
Route::get('/notifications/{userId}', [NotificationController::class, 'getAll']);

// Route to get all unread notifications for a specific user
Route::get('/notifications/{userId}/unread', [NotificationController::class, 'getUnread']);

// Route to mark a specific notification as read
Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);


?>