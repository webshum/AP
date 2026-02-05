<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class PostController {
    public function index() {
        return 'index';
    }

    public function create(Request $request) {
        return 'create';
    }

    public function store(Request $request) {
        return 'store';
    }

    public function show(int $id) {
        return 'show';
    }

    public function edit(int $id) {
        return 'edit';
    }

    public function update(int $id) {
        return 'update';
    }

    public function destroy(int $id) {
        return 'destory';
    }
}