<template>
    <div class="login-page bg-light min-vh-100">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-5 offset-lg-1">
                    <div class="bg-white shadow rounded">
                        <div class="form-left h-100 py-5 px-5">
                            <h3 class="mb-3">HGO Admin Login</h3>
                            <form action="" class="row g-4">
                                <div class="col-12">
                                    <label>Username<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                        <input type="text" class="form-control" placeholder="Enter Username">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label>Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                        <input type="text" class="form-control" placeholder="Enter Password">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="inlineFormCheck">
                                        <label class="form-check-label" for="inlineFormCheck">Remember me</label>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <a href="#" class="float-end text-primary">Forgot Password?</a>
                                </div>

                                <div class="col-12">
                                    <button type="submit"
                                        class="btn btn-primary px-4 float-end mt-4 text-white">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-end text-secondary mt-3">Hoguom Opera</p>
                </div>
            </div>
        </div>
    </div>
</template>