<?php

namespace App\Http\Controllers;

use Cloudinary;
use App\Models\Section;
use App\Models\Character;
use App\Models\Owner;
use App\Models\Classes;
use App\Models\Color_Class;
use App\Models\Color;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_section()
    {
        $sections = Section::all();
        return $sections;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_character()
    {
        // $characters = Character::with('section')->get();

        // return $characters;
        $sections = Section::with('characters')->get();

        return $sections->toArray();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_owner()
    {
        $owners = Owner::all();

        return $owners;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_class()
    {
        $classes = Classes::all();

        return $classes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_color_class()
    {
        $color_classes = Color_Class::all();
        return $color_classes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_color()
    {
        // $characters = Character::with('section')->get();

        // return $characters;
        $colors = Color_Class::with('colors')->get();

        return $colors->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_section(Request $request)
    {
        $section = Section::create(['section' => $request->section]);
        $order = Section::where('id', $section->id)->update(['order' => $section->id]);

        return response($section, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_character(Request $request)
    {
        $character = Character::create(['section_id' => (int)$request->section_id, 'name' => $request->name]);
        $order = Character::where('id', $character->id)->update(['order' => $character->id]);

        return response($character, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_owner(Request $request)
    {
        $owner = Owner::create(['name' => $request->name]);
        $order = Owner::where('id', $owner->id)->update(['order' => $owner->id]);

        return response($owner, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_class(Request $request)
    {
        $class = Classes::create(['class' => $request->class]);
        $order = Classes::where('id', $class->id)->update(['order' => $class->id]);

        return response($class, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_color_class(Request $request)
    {
        $color_class = Color_Class::create(['color_class' => $request->color_class]);
        $order = Color_Class::where('id', $color_class->id)->update(['order' => $color_class->id]);

        return response($color_class, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_color(Request $request)
    {
        $color = Color::create(['color_class_id' => (int)$request->color_class_id, 'color' => $request->color]);
        $order = Color::where('id', $color->id)->update(['order' => $color->id]);

        return response($color, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_section($id)
    {
        $section = Section::where('id', $id)
                      ->first();

        return $section ?? abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_character($id)
    {
        $character = Character::where('id', $id)
                        ->with(['section'])->first();

        return $character ?? abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_owner($id)
    {
        $owner = Owner::where('id', $id)
                    ->first();

        return $owner ?? abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_class($id)
    {
        $class = Classes::where('id', $id)
                    ->first();

        return $class ?? abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_color_class($id)
    {
        $color_class = Color_Class::where('id', $id)
                      ->first();

        return $color_class ?? abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_color($id)
    {
        $color = Color::where('id', $id)
                        ->with(['color_class'])->first();

        return $color ?? abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_section(Request $request, $id)
    {
        $section = Section::where('id', $id)
                     ->update(['section' => $request->section]);

        return $section;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_character(Request $request, $id)
    {
        $character = Character::where('id', $id)
                          ->update(['section_id' => $request->section_id, 'name' => $request->name]);

        return $character;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_owner(Request $request, $id)
    {
        $owner = Owner::where('id', $id)
                          ->update(['name' => $request->name]);

        return $owner;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_class(Request $request, $id)
    {
        $class = Classes::where('id', $id)
                          ->update(['class' => $request->class]);

        return $class;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_color_class(Request $request, $id)
    {
        $color_class = Color_Class::where('id', $id)
                     ->update(['color_class' => $request->color_class]);

        return $color_class;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_color(Request $request, $id)
    {
        $color = Color::where('id', $id)
                          ->update(['color_class_id' => $request->color_class_id, 'color' => $request->color]);

        return $color;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_section($id)
    {
        $section = Section::where('id', $id)
                          ->delete();

        return $section ?? abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_character($id)
    {
        $character = Character::where('id', $id)
                          ->delete();

        return $character ?? abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_owner($id)
    {
        DB::beginTransaction();

        try {
            $costumes = Owner::find($id)
                        ->costumes->toArray();
            $public_ids_null = array_column($costumes, 'public_id'); // ない場合$public_id = null
            $public_ids =  array_filter($public_ids_null);

            $owner = Owner::where('id', $id)
                        ->delete();      

            DB::commit();

            if($owner === 0){
                throw new Exception('意図されない処理が実行されました。');
            }

            foreach($public_ids as $public_id){
                if($public_id){
                    Cloudinary::destroy($public_id);
                }                    
            }
        }catch (\Exception $exception) {
            DB::rollBack();
            
            throw $exception;
        }

        return $owner ?? abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_class($id)
    {
        DB::beginTransaction();

        try {
            $costumes = Classes::find($id)
                          ->costumes->toArray();
            $public_ids_null = array_column($costumes, 'public_id'); // ない場合$public_id = null
            $public_ids =  array_filter($public_ids_null);

            $class = Classes::where('id', $id)
                        ->delete();      

            DB::commit();

            if($class === 0){
                throw new Exception('意図されない処理が実行されました。');
            }

            foreach($public_ids as $public_id){
                if($public_id){
                    Cloudinary::destroy($public_id);
                }                    
            }
        }catch (\Exception $exception) {
            DB::rollBack();
            
            throw $exception;
        }

        return $class ?? abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_color_class($id)
    {
        DB::beginTransaction();

        try {
            $colors = Color_Class::find($id)
                        ->colors->toArray();
            if(count($colors) < 1){
                DB::commit();
                $color_class = Color_Class::find($id)
                        ->delete();
            }else{
                $color_ids_null = array_column($colors, 'id'); // ない場合$public_id = null
                $color_ids =  array_filter($color_ids_null);
            
                foreach($color_ids as $index => $color_id){
                    $costumes = Color::find($color_id)
                                 ->costumes->toArray();
                    $public_ids_null = array_column($costumes, 'public_id'); // ない場合$public_id = null
                    $public_ids =  array_filter($public_ids_null);

                    $color = Color::where('id', $color_id)
                                ->delete();      

                    DB::commit();

                    if($color === 0){
                        throw new Exception('意図されない処理が実行されました。');
                    }

                    foreach($public_ids as $public_id){
                        if($public_id){
                            Cloudinary::destroy($public_id);
                        }                    
                    }

                    if($index === count($color_ids)-1){
                        $color_class = Color_Class::find($id)
                            ->delete();
                    }
                }
            }       
        }catch (\Exception $exception) {
            DB::rollBack();
            
            throw $exception;
        }

        return $color_class ?? abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_color($id)
    {
        DB::beginTransaction();

        try {
            $costumes = Color::find($id)
                          ->costumes->toArray();
            $public_ids_null = array_column($costumes, 'public_id'); // ない場合$public_id = null
            $public_ids =  array_filter($public_ids_null);

            $color = Color::where('id', $id)
                        ->delete();      

            DB::commit();

            if($color === 0){
                throw new Exception('意図されない処理が実行されました。');
            }

            foreach($public_ids as $public_id){
                if($public_id){
                    Cloudinary::destroy($public_id);
                }                    
            }
        }catch (\Exception $exception) {
            DB::rollBack();
            
            throw $exception;
        }

        return $color ?? abort(404);
    }
}
