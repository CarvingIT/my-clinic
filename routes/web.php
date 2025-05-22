<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FollowupImageController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AnalyticsController;


use Illuminate\Support\Facades\App;

use App\Http\Controllers\UploadController;

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// locale

Route::get('/set-locale/{locale}', function ($locale) {
    // Validate if the locale is either 'en' or 'mr'
    if (in_array($locale, ['en', 'mr'])) {
        session(['locale' => $locale]);  // Storing selected locale in session
        App::setLocale($locale);  // Setting the application's locale
    }

    return redirect()->back();  // Redirect back to the previous page
})->name('setLocale');
//end locale


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/patients/{patient}/export-pdf', [PatientController::class, 'exportPdf'])->name('patients.export-pdf');

    Route::get('/patients/{patient}/certificate', [PatientController::class, 'generateCertificate'])->name('patients.certificate');
    Route::get('/followups/{followup}/edit', [FollowUpController::class, 'edit'])->name('followups.edit');
});

// Route::resource('users',UserController::class);
// Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

// Route::resource('patients', PatientController::class);
// Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
// Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');

// Route::get('patients/{patient}', [PatientController::class, 'show'])->name('patients.show');


// Route::resource('followups', FollowUpController::class)->only(['store']);


// Route::get('followups/create', [FollowUpController::class,'create'])->name('followups.create');
// Route::post('followups', [FollowUpController::class,'store'])->name('followups.store');


Route::resource('users', UserController::class);
Route::resource('patients', PatientController::class);
Route::get('followups/create', [FollowUpController::class, 'create'])->name('followups.create');
Route::resource('followups', FollowUpController::class)->only(['create', 'store']);


Route::resource('followups', FollowUpController::class)->only(['store']);

Route::resource('followups', FollowUpController::class)->except(['create', 'store']);

Route::resource('reports', ReportController::class)->only(['store','destroy']);


Route::get('/followups', [FollowUpController::class, 'index'])->name('followups.index');

//Route::get('/followups/export', [FollowUpController::class, 'exportCSV'])->name('followups.export');

Route::get('/export-followups', [FollowUpController::class, 'exportFollowUps'])->name('followups.export');

Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store'); // Route to handle file uploads
Route::delete('/uploads/{id}', [UploadController::class, 'destroy'])->name('uploads.destroy'); // Route to delete files

// Route to serve private files
Route::get('/uploads/{id}', [UploadController::class, 'show'])->name('uploads.show');
Route::get('/followup-images/{filename}', [FollowupImageController::class, 'show'])->name('followup.image');

Route::get('/patients/{patient}/followup-images', [FollowUpImageController::class, 'showFollowUpImages'])
    ->name('followup.images');


// Routes for queue management
Route::post('/patients/{patient}/queue', [QueueController::class, 'addToQueue'])->name('patients.queue');
Route::get('/queue', [QueueController::class, 'showQueue'])->name('queue.index');
Route::delete('/queue/{queue}', [QueueController::class, 'removeFromQueue'])->name('queue.remove');
Route::post('/queue/{queue}/in', [QueueController::class, 'markIn'])->name('queue.in');

// Routes for Analytics
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');




// Patient Routes
// Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
// Route::get('patients/create', [PatientController::class, 'create'])->name('patients.create');
// Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
// Route::get('patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
// Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
// Route::put('patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
// Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');



require __DIR__ . '/auth.php';
