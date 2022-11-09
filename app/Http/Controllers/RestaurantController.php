<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Restaurants');
    }
    public function getRestaurantList(Request $request)
    {
        $keyword = "Bang sue";
        if($request->has('keyword')){ // Check Request Has Keyword ? 
            $keyword = $request->input('keyword');
        }
        // Cache Keyword Searching. If not caching search them and store to file cache 
        $value = Cache::remember($keyword, env('CACHE_TIMER_SECOND'), function () use ($keyword) {
            $urlGeocode = $this->getLatLngByKeyword($keyword);
            $responseGeocode = Http::get($urlGeocode);
        if($responseGeocode['status']=="OK"){
            // Found Lat & Long 
            $data = json_decode($responseGeocode->getBody());
            $lat = $data->results[0]->geometry->location->lat;
            $lng = $data->results[0]->geometry->location->lng;
            $urlNearbySearch = $this->urlGeneratorNearbySearch($lat, $lng);
            $responseNearbySearch = Http::get($urlNearbySearch);
            
            return $responseNearbySearch->json();
        }else{
            // Not Found Lat & Long
            return response()->json([
                'results' => [],
                'status' => 'Not Found',
            ],200);
        }
        });
        return $value;
        
    }
    private function urlGeneratorNearbySearch($lat,$long,$type="restaurant",$lang="th"){
        $url ="https://maps.googleapis.com/maps/api/place/nearbysearch/json";
        $url =$url."?keyword=".$type;
        $url =$url."&location=".$lat.'%2C'.$long;
        $url =$url."&radius=1500";
        $url =$url."&language=th";
        $url =$url."&type=".$type;
        $url =$url."&key=".env('APP_GOOGLEMAP_KEY');
        return $url;
    }
    private function getLatLngByKeyword($keyword){
        $url = "https://maps.googleapis.com/maps/api/geocode/json";
        $url =$url."?address=".$keyword;
        $url =$url."&key=".env('APP_GOOGLEMAP_KEY');
        return $url;
    }
}
