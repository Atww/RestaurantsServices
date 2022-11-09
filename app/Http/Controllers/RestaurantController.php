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
    /**
     * Get Restaurant List From Google Map Api By Keyword
     *
     * @param  \Illuminate\Http\Request  $request
     * @query Keyword
     * @return Json 
     */
    public function getRestaurantList(Request $request)
    {
        $keyword = "Bang sue"; // Default Keyword
        if($request->has('keyword')){ // Check Request Has Keyword ? 
            $keyword = $request->input('keyword');
        }
        // Cache Keyword Searching. If not caching search them and store to file cache 
        $value = Cache::remember($keyword, env('CACHE_TIMER_SECOND'), function () use ($keyword) {
            $urlGeocode = $this->getLatLngByKeyword($keyword);
            $responseGeocode = Http::get($urlGeocode); // Google Map Will Response Json with Geocode Location  Ex https://developers.google.com/maps/documentation/javascript/examples/geocoding-simple
        if($responseGeocode['status']=="OK"){ // If Google Map APi Response Found Location By Keyword
            $data = json_decode($responseGeocode->getBody());
            $lat = $data->results[0]->geometry->location->lat;
            $lng = $data->results[0]->geometry->location->lng;
            $urlNearbySearch = $this->urlGeneratorNearbySearch($lat, $lng); // Finding Nearby Restaurant Place By Location ( Lat , Long)
            $responseNearbySearch = Http::get($urlNearbySearch); // Google Map Will response json with List of Restaurant in Location
            
            return $responseNearbySearch->json();
        }else{ // If Google Map Api Not Found Location By Keyword
            // Not Found Lat & Long
            return response()->json([
                'results' => [],
                'status' => 'Not Found',
            ],200);
        }
        });
        return $value; // Return Value From Cache or New Search 
        
    }
    /**
     * Generate Url Google API NearbySearch
     *
     * @param  float  $lat
     * @param  float  $long
     * @param  int  $radius with keyword or name Maxmimum 50,000 meters
     * @param  string  $type  Example https://developers.google.com/maps/documentation/places/web-service/search-nearby#type
     * @param  string  $lang  List Supported Lang https://developers.google.com/maps/faq#languagesupport
     * @return string   URL API With Parameter
     */
    private function urlGeneratorNearbySearch($lat,$long,$radius=1500,$type="restaurant",$lang="th"){
        $url ="https://maps.googleapis.com/maps/api/place/nearbysearch/json";
        $url =$url."?keyword=".$type;
        $url =$url."&location=".$lat.'%2C'.$long;
        $url =$url."&radius=".$radius;
        $url =$url."&language=".$lang;
        $url =$url."&type=".$type;
        $url =$url."&key=".env('APP_GOOGLEMAP_KEY');
        return $url;
    }
    /**
     * Generate Url Google API NearbySearch
     *
     * @param  string  $Keyword name of  ['provice','Area','Sub-area'] Likely 'Bang Sue' , 'Khao Yoi', ... 
     * @return string   URL API With Parameter
     */
    private function getLatLngByKeyword($keyword){
        $url = "https://maps.googleapis.com/maps/api/geocode/json";
        $url =$url."?address=".$keyword;
        $url =$url."&key=".env('APP_GOOGLEMAP_KEY');
        return $url;
    }
}
