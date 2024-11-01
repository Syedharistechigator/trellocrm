<?php

namespace App\Traits;

use Exception;
use File;
use Intervention\Image\Facades\Image as InterventionImage;

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
trait BoardListCardCoverImageTrait
{
    /** Uncomment AS Required */
    protected array $imageTypes = [
//        'background' ,
//        'hero' ,
//        'banner' ,
//        'blog' ,
//        'logo_rectangle' ,
//        'logo_square' ,
//        'favicon' ,
//        'social_media_icon' ,
//        'lightbox' ,
        'thumbnail',
        'thumbnail1',
    ];
    /** Uncomment AS Required */
    private array $imageSizes = [
//        'background' => [1920, 1080],
//        'hero' => [1280, 720],
//        'banner' => [250, 250],
//        'blog' => [1200, 630],
//        'logo_rectangle' => [250, 100],
//        'logo_square' => [100, 100],
//        'favicon' => [16, 16],
//        'social_media_icon' => [32, 32],
//        'lightbox' => [1600, 500],
        'thumbnail' => [150, 150],
        'thumbnail1' => [50, 50],
    ];

    public function cover_image_url_trait($board_list_card, $type = null): ?string
    {
        if (!$board_list_card->cover_image) {
            return null;
        }
        $image = $board_list_card->cover_image;
        $client_id = optional($board_list_card)->client_id;
        $card_id = $board_list_card->card_id ?? $board_list_card->id;
        $path = "assets/images/board-list-card";
        $directories = [];
        if (!$type) {
            $directories = [
//                "{$path}/{$image}",
                "{$path}/original/{$image}",
//                "{$path}/attachments/{$image}",
//                "{$path}/activities/{$image}",
//                "{$path}/activities/{$client_id}/{$image}",
//                "{$path}/activities/random-client/{$image}",
//                "{$path}/activities/random-client/original/{$image}",
//                "{$path}/activities/{$card_id}/{$image}",
            ];
        }
        if ($type && isset($this->imageSizes[$type])) {
            [$width, $height] = $this->imageSizes[$type];
            $resizedPath1 = "{$path}/{$width}x{$height}/{$image}";
            if (file_exists(public_path($resizedPath1))) {
                return asset($resizedPath1);
            }
        }
        foreach ($directories as $directory) {
            $fullPath = public_path($directory);
            if (file_exists($fullPath)) {
                return asset($directory);
            }
        }
        return null;
    }

    private function delete_existing_images($board_cover_image)
    {
        if ($board_cover_image) {
            $board_list_path = public_path('assets/images/board-list-card/');
            $original_image_path = "{$board_list_path}original/{$board_cover_image}";
            if (File::exists($original_image_path)) {
                File::delete($original_image_path);
            }
            foreach ($this->imageSizes as [$width, $height]) {
                $fullPath = "{$board_list_path}{$width}x{$height}/{$board_cover_image}";
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private function create_cover_image_trait($request, $board_list_card, $image = null, $original_name = null, $additional_path = null, $image_name = null, $image_path = null, $set_cover = false)
    {
        try {
            ini_set('memory_limit', '2G');
            $auth_id = optional(auth()->user())->id;
            $department_id = optional($board_list_card->getBoardList->getDepartment)->id ?? null;
            $board_list_card_id = optional($board_list_card)->id ?? null;
            $additional_path = $additional_path ?? "{$department_id}/{$board_list_card_id}/";
            $original_path = public_path("assets/images/board-list-card/original/");
//        $attachment_path = public_path("assets/images/board-list-card/original/");
            $image_path = $image_path ?? $original_path;
//        $board_cover_image = $board_list_card->cover_image;
            if ($image) {
                $original_name = $original_name ?? ($image->getClientOriginalName() ?? null);
                $image_name = $image_name ?? (mt_rand() . mt_rand() . mt_rand() . time() . '00' . $auth_id . rand(111, 999) . '_' . ($original_name  ??  ('.' . $image->getClientOriginalExtension())));
                $full_image_path = $image_path . $additional_path . $image_name;
                if (!File::exists($full_image_path)) {
                    $image->move($image_path . $additional_path, $image_name);
                }
                if ($image_path != $original_path && !File::exists($original_path . $additional_path . $image_name) && File::exists($image_path . $additional_path . $image_name)) {
                    if (!File::exists($original_path . $additional_path)) {
                        File::makeDirectory($original_path . $additional_path, 0755, true);
                    }
                    copy($image_path . $additional_path . $image_name, $original_path . $additional_path . $image_name);
                }
//            if (!File::exists($attachment_path . $additional_path . $image_name)) {
//                if (!File::exists($attachment_path . $additional_path)) {
//                    File::makeDirectory($attachment_path . $additional_path, 0755, true);
//                }
//                copy($image_path . $additional_path . $image_name, $attachment_path . $additional_path . $image_name);
//            }
//              $this->delete_existing_images($board_cover_image);
                foreach ($this->imageSizes as [$width, $height]) {
                    $resizedImagePath = public_path("assets/images/board-list-card/{$width}x{$height}/");
                    if (!File::exists($resizedImagePath . $additional_path)) {
                        File::makeDirectory($resizedImagePath . $additional_path, 0755, true);
                    }
                    $resizedImage = InterventionImage::make($full_image_path);
                    $originalWidth = $resizedImage->width();
                    $originalHeight = $resizedImage->height();
                    $maxSize = $height;
                    if ($originalWidth >= $originalHeight) {
                        $newWidth = $maxSize;
                        $newHeight = (int)($originalHeight * ($maxSize / $originalWidth));
                    } else {
                        $newHeight = $maxSize;
                        $newWidth = (int)($originalWidth * ($maxSize / $originalHeight));
                    }
                    $resizedImage = $resizedImage->resize($newWidth, $newHeight, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    })->resizeCanvas($newWidth, $newHeight);
                    $resizedImage->save($resizedImagePath . $additional_path . $image_name);
                }
                if ($set_cover || (!$board_list_card->cover_image) || ($board_list_card->cover_image && !is_file($original_path . $additional_path . $image_name)) || (isset($request) && $request->has('cover_image'))) {
                    $board_list_card->cover_image = $additional_path . $image_name;
                }
                if ($request && $request->has('cover_background_color')) {
                    $board_list_card->cover_background_color = $request->cover_background_color;
                }
            }
            return $board_list_card;
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Memory limit exceeded: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        } finally {
            ini_restore('memory_limit');
        }
    }
}
