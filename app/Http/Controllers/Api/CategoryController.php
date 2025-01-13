<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;

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
        $name = $request->query('name');
        $categories = $this->categoryService->getCategories($name);

        return $this->success($categories, 'Categories fetched successfully.', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data);

        return $this->success($category, 'Category created successfully!', 201);
    }

    /**
     * Display the specified resource along with its courses.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        $category = $this->categoryService->getCategory($category);

        return $this->success($category, 'Category details fetched successfully.', 200);
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
        $data = $request->validated();
        $updatedCategory = $this->categoryService->updateCategory($category, $data);

        return $this->success($updatedCategory, 'Category updated successfully!', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);

        return $this->success(null, 'Category deleted successfully!', 200);
    }
    /**
 * Display a listing of trashed categories.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function trashed()
{
    $categories = $this->categoryService->getTrashedCategories();

    return $this->success($categories, 'Trashed categories fetched successfully.', 200);
}

/**
 * Restore a soft-deleted category.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function restore($id)
{
    $restoredCategory = $this->categoryService->restoreCategory($id);

    return $this->success($restoredCategory, 'Category restored successfully!', 200);
}

/**
 * Permanently delete a soft-deleted category.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function forceDelete($id)
{
    $this->categoryService->forceDeleteCategory($id);

    return $this->success(null, 'Category permanently deleted!', 200);
}

}
