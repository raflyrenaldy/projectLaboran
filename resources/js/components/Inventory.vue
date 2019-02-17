<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Inventory</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Inventory 
                    <i class="fas fa-plus-square fa-fw"></i>
                </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Petugas</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="inventory in Inventories.data" :key="inventory.id">
                    <td>{{inventory.id}}</td>
                    <td>{{inventory.get_user.name}}</td>
                    <td>{{inventory.name }}</td>
                    <td>{{inventory.jumlah }}</td>
                    <td>{{inventory.keterangan }}</td>
                    <td>
                        <a href="#" @click="editModal(inventory)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteInventories(inventory.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="Inventories" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- Modal -->
      <div v-if="!$gate.isAdminOrUser()" >
        <not-found></not-found>
      </div>
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 v-show="!editMode" class="modal-title" id="addNewLabel">Tambah Data</h5>
        <h5 v-show="editMode" class="modal-title" id="addNewLabel">Edit Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editMode ? updateInventories() : createInventories()">
      <div class="modal-body">        
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Nama inventory" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.jumlah" type="number" name="jumlah" placeholder="Jumlah" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah') }">
      <has-error :form="form" field="jumlah"></has-error>
    </div>
    <div class="form-group">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="Keterangan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
    </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button v-show="editMode" type="submit" class="btn btn-success">Simpan</button>
        <button v-show="!editMode" type="submit" class="btn btn-primary">Tambah Baru</button>
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
                Inventories: {},
                form: new Form({
                    id: '',
                    id_user: '',
                    name : '',
                    jumlah : '',
                    keterangan : ''
                })
            }
        },
        methods: {
          getResults(page = 1) {
            axios.get('api/inventory?page=' + page)
              .then(response => {
                this.Inventories = response.data;
              });
          }, 
          updateInventories(){
            this.$Progress.start();
            this.form.put('api/inventory/'+this.form.id)

            .then(() => {
              Fire.$emit('AfterCreated');
            $('#addNew').modal('hide');

            Swal.fire(
                          'Telah Diperbarui!',
                          'Data sudah berhasil diperbarui!',
                          'success'
                        )
            this.$Progress.finish();
            })
            .catch(() => {
                Toast.fire({
              type: 'error',
              title: 'Gagal Perbarui data!'
            })
              this.$Progress.fail();
            });            

          },
          newModal(){
            this.editMode = false;
            this.form.reset();
            $('#addNew').modal('show');
            
          },
          editModal(inventory){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(inventory);
          },
          deleteInventories(id){
            Swal.fire({
                title: 'Apakah Anda Yakin??',
                text: "Anda Akan Menghapus Data ini!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Data Ini!'
              }).then((result) => {

                if(result.value) {
                      //send request to the server
                      this.form.delete('api/inventory/'+id).then(()=>{
                        Fire.$emit('AfterCreated');                 
                        Swal.fire(
                          'Sudah Dihapus!',
                          'Data Sudah Berhasil Dihapus',
                          'success'
                        )
                      
                      }).catch(()=>{

                      })
                }                
              })
          },
          loadInventories(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/inventory")
            .then(({data}) => (this.Inventories = data));
            }
          },
          createInventories(){
            this.$Progress.start();
            this.form.post('api/inventory')
            .then(()=>{
            // Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'inventory Berhasil Dibuat!'
            })
            this.Inventories.unshift(response.data);

            this.$Progress.finish()

            })
            .catch(()=>{
              this.$Progress.fail();
            })
            

          }
        },
        mounted() {
          Echo.private('inventories')
            .listen('sendInventories',(e) =>{
              console.log(e);
              this.Inventories.unshift(e);
            })
            Fire.$on('searching',()=>{
              let query = this.$parent.search;
              axios.get('api/findInventory?q=' + query)
              .then((data)=>{
                this.Inventories = data.data
              })
              .catch(()=>{

              });
            });
            
            this.loadInventories();
            Fire.$on('AfterCreated',() =>{
              this.loadInventories();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
