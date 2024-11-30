<?php

namespace App\Http\Controllers\Api\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:api');
        $this->categoryService = $categoryService;
    }
 /**
     * Display a listing of the resource with optional name filter.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            
            $name = $request->query('name');

            // Get categories with optional filtering
            $categories = $this->categoryService->getCategories($name);

            return $this->success($categories, 200);
        } catch (\Exception $e) {
            return response()->json('error Failed to fetch categories.', 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
           

            $category = $this->categoryService->createCategory($data);

            return $this->success($category, 'Category created successfully!', 201);
        } catch (\Exception $e) {
            return $this->error('errorFailed to create category.', 500);
        }
    }

    /**
     * Display the specified resource along with its courses.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        try {
            // Get category with courses by ID
            $category = $this->categoryService->getCategory($category);

            return $this->success($category, 200);
        } catch (\Exception $e) {
            return $this->error('errorFailed to fetch category details.', 500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $data = $request->validated();

            $updatedCategory = $this->categoryService->updateCategory($category, $data);

            return $this->success($updatedCategory, 'Category updated successfully!', 200);
        } catch (\Exception $e) {
            return $this->error('errorFailed to update category.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        try {
            $this->categoryService->deleteCategory($category);

            return $this->success(null,'Category deleted successfully!', 200);
        } catch (\Exception $e) {
            return $this->error('error Failed to delete category.', 500);
        }
    }
}
