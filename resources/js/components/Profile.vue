<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background-color: coral;">
                <h3 class="widget-user-username">{{this.form.name}}</h3>
                <h5 class="widget-user-desc">{{this.form.type}}</h5>
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
            </div>
            <div class="col-md-12 mt-3">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                  <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <div class="user-block">
                        <img class="img-circle img-bordered-sm" src="" alt="user image">
                        <span class="username">
                          <a href="#">Jonathan Burke Jr.</a>
                          <a href="#" class="float-right btn-tool"><i class="fa fa-times"></i></a>
                        </span>
                        <span class="description">Shared publicly - 7:30 PM today</span>
                      </div>
                      <!-- /.user-block -->
                      <p>
                        Lorem ipsum represents a long-held tradition for designers,
                        typographers and the like. Some people hate it and argue for
                        its demise, but others ignore the hate as they create awesome
                        tools to help create filler text for everyone from bacon lovers
                        to Charlie Sheen fans.
                      </p>

                      <p>
                        <a href="#" class="link-black text-sm mr-2"><i class="fa fa-share mr-1"></i> Share</a>
                        <a href="#" class="link-black text-sm"><i class="fa fa-thumbs-o-up mr-1"></i> Like</a>
                        <span class="float-right">
                          <a href="#" class="link-black text-sm">
                            <i class="fa fa-comments-o mr-1"></i> Comments (5)
                          </a>
                        </span>
                      </p>

                      <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                    </div>
                    <!-- /.post -->


                  </div>                 

                  <div class="tab-pane active show" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group">
                        <label for="inputName" class="col-sm-2 control-label">Name</label>

                        <div class="col-sm-10">
                          <input v-model="form.name" name="name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="inputName" placeholder="Name">
                          <has-error :form="form" field="name"></has-error>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-10">
                          <input v-model="form.email" name="email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" id="inputEmail" placeholder="Email">
                          <has-error :form="form" field="email"></has-error>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputName2" class="col-sm-2 control-label">Username</label>

                        <div class="col-sm-10">
                          <input v-model="form.username" name="username" type="text" class="form-control" id="inputName2" placeholder="Username">
                        </div>
                      </div>
                        <div class="form-group">
                          <label for="inputName2" class="col-sm-2 control-label">Photo</label>
                        <div class="col-sm-10">
                          <input name="photo" @change="updateProfile" type="file" class="form-control-file" :class="{ 'is-invalid': form.errors.has('photo') }" id="exampleFormControlFile1">
                          <has-error :form="form" field="photo"></has-error>
                        </div>
                        </div>
                      <div class="form-group">
                        <label for="inputName2" class="col-sm-2 control-label">Address</label>

                        <div class="col-sm-10">
                          <textarea v-model="form.address" name="address" type="text" class="form-control" id="inputName2" placeholder="Address">
                          </textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputName2" class="col-sm-2 control-label">Phone</label>

                        <div class="col-sm-10">
                          <input v-model="form.phone" name="phone" type="number" class="form-control" id="inputName2" placeholder="Phone">
                          
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputSkills" class="col-sm-2 control-label">Password</label>

                        <div class="col-sm-10">
                          <input type="password" v-model="form.password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" id="inputSkills" placeholder="password">
                          <has-error :form="form" field="password"></has-error>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button type="submit" @click.prevent="updateInfo" class="btn btn-danger">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
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
                    name : '',
                    username : '',
                    email : '',
                    password : '',
                    type : '',
                    phone : '',
                    address : '',
                    photo : ''
                })
          }
        },

        mounted() {
            Fire.$on('AfterCreated',() =>{
              this.created();
            });
        },

        methods:{
          getProfilePhoto(){
            let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/"+ this.form.photo ;
            return photo;
          },
          updateInfo(){
            this.$Progress.start();
            this.form.put("api/profile")
            .then(()=>{
              Fire.$emit('AfterCreate');
              Toast.fire({
              type: 'success',
              title: 'User Updated in successfully'
            })
              this.$Progress.finish();

            })
            .catch(()=>{
              this.$Progress.fail();

            });
          },
          updateProfile(e){
            // console.log('uploading file...');
            let file = e.target.files[0];
            let reader = new FileReader();

            if(file['size'] < 2111775){
              reader.onloadend = (file) => {
              // console.log('RESULT', reader.result)
              this.form.photo = reader.result;
              }
              reader.readAsDataURL(file);
            }else{
              Swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'You are uploading a large file',
              })
            }
            
          }

        },
         created() {
          axios.get("api/profile")
          .then(({ data }) => (this.form.fill(data)));
        }
    }
   
</script>
