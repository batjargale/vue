<style>
    .widget-user-header{
        background-position: center center;
        background-size: cover;
        height: 250px !important;
    }
</style>
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-white" style="background-image:url('./images/user-cover.jpg')">
                        <h3 class="widget-user-username text-center black">{{ this.form.name }}</h3>
                        <h5 class="widget-user-desc text-center black">{{ this.form.type }}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                            <h5 class="description-header">3,200</h5>
                            <span class="description-text">SALES</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                            <h5 class="description-header">13,000</h5>
                            <span class="description-text">FOLLOWERS</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4">
                            <div class="description-block">
                            <h5 class="description-header">35</h5>
                            <span class="description-text">PRODUCTS</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>

                <!-- Edit Profile -->
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>

                        <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="activity">
                                <!-- Post -->
                                <div class="post">
                                    <div class="user-block">
                                        Activity tab
                                    </div>
                                <!-- /.user-block -->
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane active" id="settings">
                                <form class="form-horizontal" @submit.prevent="updateProfile()">
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                        <div class="col-sm-10">
                                            <input v-model="form.name" type="text" name="name" placeholder="Name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                            <has-error :form="form" field="name"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input v-model="form.email" type="text" name="email" placeholder="Email Address" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                            <has-error :form="form" field="email"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputExperience" class="col-sm-2 col-form-label">Bio</label>
                                        <div class="col-sm-10">
                                            <textarea v-model="form.bio" name="bio" placeholder="Short bio for user (Optional)" class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" ></textarea>
                                            <has-error :form="form" field="bio"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputImage" class="col-sm-2 col-form-label">Cover Image</label>
                                        <div class="col-sm-10">
                                            <input name="photo" type="file" placeholder="Cover Image" @change="updateProfile">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" name="password" placeholder="Password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                            <has-error :form="form" field="password"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" @click.prevent="updateInfo" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                            <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''

                })
            }
        },

        mounted() {
            console.log('Component mounted.')
        },

        methods:{
            getProfilePhoto(){
                // return "images/profile/"+ this.form.photo;
                // let prefix = (this.form.photo.match(/\//) ? '' : '/images/profile/');
                // return prefix + this.form.photo;
                let photo = (this.form.photo.length > 200) ? this.form.photo : "images/profile/" + this.form.photo;
                return photo;
            },
            updateInfo(){
                this.$Progress.start();
                if(this.form.password ==''){
                    this.form.password = undefined;
                }
                this.form.put('api/profile/')
                .then(()=>{
                    Fire.$emit('AfterCreate');
                    swal.fire(
                        'Амжилттай хадгалагдлаа!',
                        'Файл хадгалагдлаа.',
                        'success'
                    )
                    this.$Progress.finish();

                })
                .catch(()=>{
                    this.$Progress.fail();
                });

            },
            updateProfile(e){
                // console.log('uploading');
                let file = e.target.files[0];
                // console.log(file.size);
                let reader = new FileReader();
                if(file.size < 2111775){
                    reader.onloadend = (file) => {
                    // console.log('RESULT', reader.result)

                    this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);
                }else{
                    swal.fire({
                        type: 'error',
                        title: 'Алдаа гарлаа',
                        text: 'Файлын хэмжээ хэтэрсэн байна!',
                    });
                }

            }
        },

        created() {
            axios.get("api/profile")
            .then(({ data }) => (this.form.fill(data)));
        },
    }
</script>
