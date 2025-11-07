<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FollowupImageController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PatientDuesController;
use App\Http\Controllers\DataAnalysisController;
use App\Http\Controllers\SyncController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\DoctorMiddleware;
use App\Models\FollowUp;
use App\Http\Controllers\PresetController;


use Illuminate\Support\Facades\App;

use App\Http\Controllers\UploadController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ImportExportController;

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


// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//     Route::get('/patients/{patient}/export-pdf', [PatientController::class, 'exportPdf'])->name('patients.export-pdf');

//     Route::get('/patients/{patient}/certificate', [PatientController::class, 'generateCertificate'])->name('patients.certificate');
//     Route::get('/followups/{followup}/edit', [FollowUpController::class, 'edit'])->name('followups.edit');
// });


// Route::resource('users', UserController::class);
// Route::resource('patients', PatientController::class);
// Route::get('followups/create', [FollowUpController::class, 'create'])->name('followups.create');
// Route::resource('followups', FollowUpController::class)->only(['create', 'store']);


// Route::resource('followups', FollowUpController::class)->only(['store']);

// Route::resource('followups', FollowUpController::class)->except(['create', 'store']);

// Route::resource('reports', ReportController::class)->only(['store','destroy']);


// Route::get('/followups', [FollowUpController::class, 'index'])->name('followups.index');

// //Route::get('/followups/export', [FollowUpController::class, 'exportCSV'])->name('followups.export');

// Route::get('/export-followups', [FollowUpController::class, 'exportFollowUps'])->name('followups.export');

// Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store'); // Route to handle file uploads
// Route::delete('/uploads/{id}', [UploadController::class, 'destroy'])->name('uploads.destroy'); // Route to delete files

// // Route to serve private files
// Route::get('/uploads/{id}', [UploadController::class, 'show'])->name('uploads.show');
// Route::get('/followup-images/{filename}', [FollowupImageController::class, 'show'])->name('followup.image');

// Route::get('/patients/{patient}/followup-images', [FollowUpImageController::class, 'showFollowUpImages'])
//     ->name('followup.images');


// // Routes for queue management
// Route::post('/patients/{patient}/queue', [QueueController::class, 'addToQueue'])->name('patients.queue');
// Route::get('/queue', [QueueController::class, 'showQueue'])->name('queue.index');
// Route::delete('/queue/{queue}', [QueueController::class, 'removeFromQueue'])->name('queue.remove');
// Route::post('/queue/{queue}/in', [QueueController::class, 'markIn'])->name('queue.in');

// // Routes for Analytics
// Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

// // Route for patient dues
// Route::get('/patient-dues', [PatientDuesController::class, 'index'])->name('patient-dues.index');


// Admin Routes
// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::resource('users', UserController::class);
//     Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
// });

// Admin Routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::resource('users', UserController::class); // Admin can manage users
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index'); // Admin can view analytics
    Route::get('/followups', [FollowUpController::class, 'index'])->name('followups.index'); // List follow-ups
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // Profile management
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Update profile
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Delete profile
    Route::get('/patient-dues', [PatientDuesController::class, 'index'])->name('patient-dues.index'); // List patient dues
    Route::get('/admin/sync-data', [SyncController::class, 'showSyncForm'])->name('admin.sync-data');
    Route::post('/admin/sync-data', [SyncController::class, 'syncData'])->name('admin.sync-data.post');
    Route::get('/admin/import-export', [ImportExportController::class, 'index'])->name('admin.import-export');
    Route::post('/admin/export-data', [ImportExportController::class, 'export'])->name('admin.export-data');
    Route::post('/admin/import-data', [ImportExportController::class, 'import'])->name('admin.import-data');
    Route::get('/admin/download-export/{file}', [ImportExportController::class, 'download'])->name('admin.download-export');
    Route::get('/admin/export-files', [ImportExportController::class, 'listExportFiles'])->name('admin.export-files');
    Route::delete('/admin/export-files/{file}', [ImportExportController::class, 'deleteExportFile'])->name('admin.export-files.delete');
});

// Staff Routes
Route::middleware(['auth', StaffMiddleware::class])->group(function () {
    Route::resource('patients', PatientController::class)->except(['destroy', 'show']); // Staff can manage patients except for deletion and viewing individual patient details
    Route::post('/patients/{patient}/queue', [QueueController::class, 'addToQueue'])->name('patients.queue'); // Staff can add patients to the queue
    Route::get('/queue', [QueueController::class, 'showQueue'])->name('queue.index'); // Staff can view the queue
    Route::delete('/queue/{queue}', [QueueController::class, 'removeFromQueue'])->name('queue.remove'); // Staff can remove patients from the queue
    // Route::post('/queue/{queue}/in', [QueueController::class, 'markIn'])->name('queue.in'); // Staff can mark patients as in
});

// Doctor Routes
Route::middleware(['auth', DoctorMiddleware::class])->group(function () {
    // Route::get('followups/create', [FollowUpController::class, 'create'])->name('followups.create'); // Doctor can create follow-ups
    Route::get('followups/create/{patient}', [FollowUpController::class, 'create'])->name('followups.create');

    Route::resource('followups', FollowUpController::class)->except(['index']); // Doctor can manage follow-ups except for listing them
    Route::resource('reports', ReportController::class)->only(['store', 'destroy']); // Doctor can store and delete reports
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store'); // Doctor can upload files
    Route::delete('/uploads/{id}', [UploadController::class, 'destroy'])->name('uploads.destroy'); // Doctor can delete uploaded files
    Route::get('/uploads/{id}', [UploadController::class, 'show'])->name('uploads.show'); // Doctor can view uploaded files
    Route::get('/patients/{patient}/certificate', [PatientController::class, 'generateCertificate'])->name('patients.certificate'); // Doctor can generate patient certificates
    Route::get('/export-followups', [FollowUpController::class, 'exportFollowUps'])->name('followups.export'); // Doctor can export follow-ups

    // Route::get('/queue', [QueueController::class, 'showQueue'])->name('queue.index'); // Doctors can view the queue
    // Route::delete('/queue/{queue}', [QueueController::class, 'removeFromQueue'])->name('queue.remove'); // Doctors can remove patients from the queue
    Route::post('/queue/{queue}/in', [QueueController::class, 'markIn'])->name('queue.in'); // Doctors can mark patients as in
});

// Shared Routes (accessible by all authenticated users)
Route::middleware('auth')->group(function () {

    Route::get('/patients/{patient}/export-pdf', [PatientController::class, 'exportPdf'])->name('patients.export-pdf'); // Export patient data as PDF

    Route::get('/followup-images/{filename}', [FollowupImageController::class, 'show'])->name('followup.image'); // Show follow-up images
    Route::get('/patients/{patient}/followup-images', [FollowUpImageController::class, 'showFollowUpImages'])->name('followup.images'); // Show follow-up images for a patient
    Route::get('/analytics/data-analysis', [DataAnalysisController::class, 'index'])->name('data-analysis.index');   // Data analysis for analytics
});

// Patient Deletion (Admin only)
Route::middleware(['auth', AdminMiddleware::class])->delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

//Export Patient JSON
// Route::middleware(['auth', DoctorMiddleware::class])->get('/patients/export/{patient}', [PatientController::class, 'exportPatientJSON'])->name('patients.export_json');


// Bulk Export Clinic Data
Route::middleware(['auth', DoctorMiddleware::class])
    ->post('/patients/export-all', [PatientController::class, 'exportAllPatientsJSON'])
    ->name('patients.export_all_json');

// Bulk Import Clinic Data
Route::middleware(['auth', DoctorMiddleware::class])
    ->post('/patients/import-all', [PatientController::class, 'importAllPatientsJSON'])
    ->name('patients.import_all_json');


// single patient export and import
Route::middleware(['auth'])->group(function () {
    Route::resource('patients', PatientController::class);
    Route::post('patients/{patient}/export-json', [PatientController::class, 'exportPatientJSON'])->name('patients.export_json');
    Route::post('patients/import-json', [PatientController::class, 'importPatientJSON'])->name('patients.import_json');
});

// Preset Routes
Route::middleware('auth')->group(function () {
    Route::get('/presets', [PresetController::class, 'index']);
    Route::post('/presets', [PresetController::class, 'store']);
    Route::post('/presets/update-order', [PresetController::class, 'updateOrder'])->name('presets.update-order');
    Route::put('/presets/{preset}', [PresetController::class, 'update']);
    Route::delete('/presets/{preset}', [PresetController::class, 'destroy']);
});


require __DIR__ . '/auth.php';


