<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helper\Helper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function getAllBanners()
    {
        // Fetch all banners from the database
        $banners = Banner::all();
        $totalBanners = $banners->count();

        return response()->json(
            [
                'status' => 'success',
                'totalBanners' => $totalBanners,
                'banners' => $banners,
            ],
            200,
        );
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Store the image in the 'public/banners' directory
            $path = $image->store('banners', 'public');


            // App URL
            $appurl = "https://elf-trusty-evidently.ngrok-free.app";

            // Generate a full URL to the image
            $url = $appurl . Storage::url($path);

            // Return the full URL so it can be accessed via the API
            return response()->json(['banner_image_url' => url($url)], 200);
        }

        return response()->json(['error' => 'Image upload failed'], 400);
    }

    /**d
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $banner = new Banner();
        $banner->banner_name = $request->banner_name;
        $banner->banner_description = $request->banner_description;
        $banner->banner_image_url = $request->banner_image_url;

        $banner->save();

        return response()->json(['message' => 'Banner created successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the banner by ID
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'banner_name' => 'sometimes|string|max:255',
            'banner_description' => 'sometimes|string',
            'banner_image_url' => 'sometimes|string',
            // 'banner_image_url' => 'sometimes|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ],
                422,
            ); // Use 422 for validation errors
        }

        // Update fields if provided
        if ($request->has('banner_name')) {
            $banner->banner_name = $request->input('banner_name');
        }

        if ($request->has('banner_description')) {
            $banner->banner_description = $request->input('banner_description');
        }

        // Handle file upload if a new image is provided
        if ($request->has('banner_image_url')) {
            $banner->banner_image_url = $request->input('banner_image_url');
        }

        $banner->save();

        return response()->json([
            'message' => 'Banner updated successfully',
            'banner' => $banner,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the banner by ID
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        // Delete the banner
        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully']);
    }
}
