<template>
    <div class="container">
       <div class="row mt-5">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Add New 
                    <i class="fas fa-user-plus fa-fw"></i>
                </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="user in users" :key="user.id">
                    <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.type | upText}}</td>
                    <td>{{user.created_at | myDate}}</td>
                    <td>
                        <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- Modal -->
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 v-show="!editMode" class="modal-title" id="addNewLabel">Add New</h5>
        <h5 v-show="editMode" class="modal-title" id="addNewLabel">Update Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editMode ? updateUser() : createUser()">
      <div class="modal-body">        
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Name" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.email" type="email" name="email" placeholder="Email Address" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
      <has-error :form="form" field="email"></has-error>
    </div>

    <div class="form-group">
    <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
    <option value="">Select User Role</option>
    <option value="Super Admin">Select User Role</option>
    <option value="Keuangan">Keuangan</option>
    <option value="Inventory">Inventory</option>
    <option value="Admin">Admin</option>
    </select>
    <has-error :form="form" field="type"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.password" type="password" id="password" name="password" placeholder="Password"
        class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
      <has-error :form="form" field="password"></has-error>
    </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button v-show="editMode" type="submit" class="btn btn-success">Save Changes</button>
        <button v-show="!editMode" type="submit" class="btn btn-primary">Create User</button>
      </div>
    </form>

    </div>
  </div>
</div>
    </div>
</template>

<script>
    export default {
        data() {
            return{
                editMode: false,
                users: {},
                form: new Form({
                    id: '',
                    name : '',
                    username : '',
                    email : '',
                    password : '',
                    type : '',
                    photo : ''
                })
            }
        },
        methods: {
          updateUser(){
            this.$Progress.start();
            this.form.put('api/user/'+this.form.id)

            .then(() => {
              Fire.$emit('AfterCreated');
            $('#addNew').modal('hide');

            Swal.fire(
                          'Updated!',
                          'Your file has been Updated.',
                          'success'
                        )
            this.$Progress.finish();
            })
            .catch(() => {
              this.$Progress.fail();
            });            

          },
          newModal(){
            this.editMode = false;
            this.form.reset();
            $('#addNew').modal('show');
            
          },
          editModal(user){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(user);
          },
          deleteUser(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {

                if(result.value) {
                      //send request to the server
                      this.form.delete('api/user/'+id).then(()=>{
                        Fire.$emit('AfterCreated');                 
                        Swal.fire(
                          'Deleted!',
                          'Your file has been deleted.',
                          'success'
                        )
                      
                      }).catch(()=>{

                      })
                }                
              })
          },
          loadUsers(){
            axios.get("api/user").then(({data}) => (this.users = data.data));
          },
          createUser(){
            this.$Progress.start();
            this.form.post('api/user')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'User Created in successfully'
            })
            this.$Progress.finish()

            })
            .catch(()=>{
              this.$Progress.fail();
            })
            

          }
        },
        mounted() {
            this.loadUsers();
            Fire.$on('AfterCreated',() =>{
              this.loadUsers();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
