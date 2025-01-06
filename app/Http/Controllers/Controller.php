<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function generateRandomQrCode($url)
    {
        // Generate a random file name
        $fileName = Str::random(10) . '.png';

        // Generate QR code
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile($url, "$fileName.png");

        // Return the path to the generated QR code
        return storage_path("app/public/qrcodes/{$fileName}");
    }


    public function toSelect2Data($args, $key_id, $key_text, $key_text_adds = null, $spec = null): array
    {
        if (!is_array($args)) {
            try {
                $args->toArray();
            } catch (\Exception $exception) {
                return [];
            }
        }

        if (count($args) == 0) {
            return [];
        }

        $result = array_filter(array_map(function ($item) use ($key_id, $key_text, $key_text_adds, $spec) {
            if ($key_text == 'running_number_text') {
                return [
                    'id' => $item[$key_id],
                    'text' => $item[($item['job_stage'] == 'incoming' ? 'running_number_inc' : 'running_number')] . ($key_text_adds ? $spec . $item[$key_text_adds] : null)
                ];
            } else {
                if (!empty($item[$key_text])) {
                    return [
                        'id' => $item[$key_id],
                        'text' => $item[$key_text] . ($key_text_adds ? $spec . $item[$key_text_adds] : null)
                    ];
                }
            }

            return null;
        }, $args));

        return array_values($result);
    }
}
