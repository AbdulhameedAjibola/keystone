<?php

namespace App\Http\Controllers;

use App\Http\Resources\CareerCollection;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCareerRequest;
use App\Http\Requests\UpdateCareerRequest;


/**
 * @group Careers management (JOBS)
 *
 * APIs for uploading and managing careers
 * it contains the following endpoints:
 * - Create Job
 * - Get one Job
 * - Get all jobs
 * - Delete Job
 * - Update Job
 * - Search Jobs
 * - Toggle job on or off
 * - Get deleted jobs
 * - Restore job
 * - Destroy job(Permanent Delete)
 * 
 */
class CareerController extends Controller
{
    /**
     * Display all the careers
     * 
     * Get all the careers
     */
    public function index()
    {
        return new CareerCollection(Career::paginate(15));
    }

   

    /**
     * create a new job
     */
    public function store(StoreCareerRequest $request)
    {
        return new CareerResource(Career::create($request->all()));
    }

    /**
     * Get one job
     */
    public function show(Career $career)
    {
        return new CareerResource($career);
    }

    
    /**
     * Update a job
     * 
     * this endpoint supports both put and patch requests
     */
    public function update(UpdateCareerRequest $request, Career $career)
    {
        $career->update($request->all());
        return new CareerResource($career);
    }

    /**
     * Soft delete a career
     */
    public function delete(Career $career)
    {
        $career->delete();
        return response()->json(['message' => "Career deleted successfully"]);
    }

     /**
     *  
     * Search for a job
     * 
     * @queryparam search string The search term, the search is done on the title of the job by default, 
     * 
     * @queryparam column string The column to search on. Optional. default: title
     * if you have other columns that you want to search on, you can add them as query parameters
     * allowed columns are: title, location, type
     * if using the type query key, the allowed values are: 'full-time','part-time','contract', 'internship'
     * 
     * 
     * @unauthenticated
     */
    public function search(Request $request){

         $allowedColumns = ['title', '', 'location', 'type'];

        $column = $request->input('column', 'title');

      if (!in_array($column, $allowedColumns)) {
            $column = 'title';
        }

        $search = $request->get('search');

        //add more where clauses as required
        $career = Career::where('title', 'LIKE', '%'. $search .'%')->paginate(5);
        return CareerResource::collection($career);
    }

    /**
     * Toggle a career on or off
     */

    public function careerToggle(Career $career){
        $career->is_active = !$career->is_active;
        $career->save();

        return new CareerResource($career);
    }

    /**
     * restore a soft deleted career
     * 
     * this endpoint takes the id of the career intended to be restored
     * you can get the id from the data restored from the get-deleted endpoint
     */

    public function restore($id){
        $career = Career::withTrashed()->findOrFail($id);
        $career->restore();

        return new CareerResource($career);
    }
    
     /**
     * get all deleted careers
     * 
     * this endpoint returns all the careers that have been soft deleted
     */
    public function getDeleted(){
        return new CareerCollection(Career::onlyTrashed()->get());
    }

   

    /**
     * get all deleted careers
     * 
     * this endpoint is to permanently delete a career
     */
    public function destroy(Career $career){
        Career::find($career->id)->forceDelete();
        return response()->json(['message' => "Career deleted successfully"]);
    }
    
}
