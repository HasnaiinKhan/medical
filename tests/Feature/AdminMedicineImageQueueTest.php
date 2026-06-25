<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminMedicineController;
use App\Jobs\ProcessMedicineImageJob;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminMedicineImageQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_dispatches_image_jobs_for_remote_urls(): void
    {
        Queue::fake();

        Category::create([
            'name' => 'Pain Relief',
            'slug' => 'pain-relief',
        ]);

        $request = Request::create('/admin/medicines', 'POST', [
            'name' => 'Test Medicine',
            'manufacturer' => 'Test Manufacturer',
            'description' => 'A test medicine',
            'mrp' => '45.00',
            'price' => '40.00',
            'prescription_required' => false,
            'stock' => 100,
            'category_id' => Category::first()->id,
            'image_url' => 'https://example.com/image.jpg',
            'extra_image_url' => ['https://example.com/extra-1.jpg'],
        ]);

        $controller = new AdminMedicineController();
        $response = $controller->store($request);

        $this->assertNotNull($response);
        $this->assertSame(1, Medicine::count());

        Queue::assertPushed(ProcessMedicineImageJob::class, 2);
        Queue::assertPushed(ProcessMedicineImageJob::class, function (ProcessMedicineImageJob $job) {
            return $job->field === 'image_url'
                && $job->remoteUrl === 'https://example.com/image.jpg';
        });
        Queue::assertPushed(ProcessMedicineImageJob::class, function (ProcessMedicineImageJob $job) {
            return $job->field === 'extra_images'
                && $job->remoteUrl === 'https://example.com/extra-1.jpg';
        });
    }
}
