<?php

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', function() use ($app) {
    return $app->welcome();
});

$app->get('/images', function () {
    $images = App\Models\Image::all();

    return response()->json($images);
});

$app->post('/images', function(Request $request) {

    $input = $request->all();

    // Validate the input against given rules
    $validation = Validator::make($input, [
        'image'     => 'required|mimes:gif,png,jpg',
        'title'     => 'required|min:3',
    ]);

    // If the validation fails, we return with a JSON error response
    if($validation->fails()) {
        return response()->json($validation->messages(), 400);
    }

    // Generate a random filename for the image and move it to the
    // "public/uploads" directory
    $image = $request->file('image');
    $filename = Str::random(16) . '.' . $image->getClientOriginalExtension();
    $image->move(__DIR__ . '/../../public/uploads', $filename);

    // Create the new image in the database
    $image = App\Models\Image::create([
        'url'   => 'uploads/' . $filename,
        'title' => $input['title']
    ]);

    // Return the newly created image
    return response()->json($image, 200);
});
