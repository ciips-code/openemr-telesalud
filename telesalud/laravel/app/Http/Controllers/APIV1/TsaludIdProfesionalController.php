<?php
namespace App\Http\Controllers\APIV1;

use App\Http\Controllers\APIV1\BaseController;
use App\Http\Resources\TsaludIdProfesionalResource;
use App\Models\TsaludIdProfesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TsaludIdProfesionalController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profesionalsIds = null;
        $limit = $this->getLimit();
        $profesionalsIds = TsaludIdProfesional::paginate($limit);
        return $this->sendResponse(TsaludIdProfesionalResource::collection($profesionalsIds));
    }

    /**
     *
     * @param unknown $user_id            
     * @return \Illuminate\Http\Response
     */
    public function filterByUserId($user_id)
    {
        $profesionalsIds = null;
        $limit = $this->getLimit();
        $profesionalsIds = TsaludIdProfesional::where('user_id', '=', $user_id)->paginate($limit);
        return $this->sendResponse(TsaludIdProfesionalResource::collection($profesionalsIds));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request            
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //
            DB::beginTransaction();
            $validated = $request->validated();
            if ($validated) {
                $input = $request->all();
                $profesional_id = TsaludIdProfesional::create($input);
                //
                DB::commit();
                return $this->sendResponse(new TsaludIdProfesionalResource($profesional_id));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param unknown $id            
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profesional_id = TsaludIdProfesional::findOrFail($id);
        //
        if ($profesional_id) {
            return $this->sendResponse(new TsaludIdProfesionalResource($profesional_id));
        } else {
            return $this->sendError('Profesional Id does not exist.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\TsaludIdProfesional $tsaludIdProfesional            
     * @return \Illuminate\Http\Response
     */
    public function edit(TsaludIdProfesional $tsaludIdProfesional)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \App\Models\TsaludIdProfesional $tsaludIdProfesional            
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TsaludIdProfesional $tsaludIdProfesional)
    {
        try {
            DB::beginTransaction();
            //
            $validated = $request->validated();
            if ($validated) {
                $input = $request->all();
                $tsaludIdProfesional->identificador_profesional = $input['identificador_profesional'];
                $tsaludIdProfesional->user_id = $input['user_id'];
                $tsaludIdProfesional->save();
                DB::commit();
                return $this->sendResponse(new TsaludIdProfesionalResource($tsaludIdProfesional));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\TsaludIdProfesional $tsaludIdProfesional            
     * @return \Illuminate\Http\Response
     */
    public function destroy(TsaludIdProfesional $tsaludIdProfesional)
    {
        if ($tsaludIdProfesional->delete()) {
            return $this->sendResponse([], 'Profesional Id deleted.');
        }
    }
}
