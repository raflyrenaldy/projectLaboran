<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Catatan Beli</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Catatan Beli 
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
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="catatanBeli in catatanBelis.data" :key="catatanBeli.id">
                    <td>{{catatanBeli.id}}</td>
                    <td>{{catatanBeli.get_user.name}}</td>
                    <td>{{catatanBeli.name }}</td>
                    <td>{{catatanBeli.harga }}</td>
                    <td>{{catatanBeli.jumlah }}</td>
                    <td>
                      <span v-if="catatanBeli.status == 'New'" class="badge badge-warning">{{catatanBeli.status }}</span>
                      <span v-if="catatanBeli.status == 'Lunas'" class="badge badge-info">{{catatanBeli.status }}</span>
                    </td>
                    <td>
                        <a href="#" @click="editModal(catatanBeli)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deletecatatanBelis(catatanBeli.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                        /
                        <a v-if="catatanBeli.status == 'Lunas'" title="Sudah Lunas" >
                            <i v-if="catatanBeli.status == 'Lunas'" class="fa fa-check green"></i>
                            </a>
                        <a href="#" v-if="catatanBeli.status == 'New'" @click="selesaiCatatanBeli(catatanBeli.id)">
                            <i v-if="catatanBeli.status == 'New'" class="fa fa-check red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="catatanBelis" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updateCatatanBelis() : createCatatanBelis()">
      <div class="modal-body">  
  
           
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Nama Yang diBeli" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.harga" type="number" name="harga" placeholder="Harga" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('harga') }">
      <has-error :form="form" field="harga"></has-error>
    </div>
    <div class="form-group">
      <input v-model="form.jumlah" type="number" name="jumlah" placeholder="Jumlah" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah') }">
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
                catatanBelis: {},
                form: new Form({
                    id: '',
                    id_user : '',
                    name : '',
                    harga : '',
                    jumlah : '',
                    status : ''
                })
            }
        },
        methods: {
          selesaiCatatanBeli(id){
            this.$Progress.start();
            this.form.get('api/catatanBeli/finish/'+id)
            .then(()=>{
               Fire.$emit('AfterCreated');
               
            Swal.fire(
                          'Selesai!',
                          'Aplikasi ini sudah Selesai!',
                          'success'
                        )
            this.$Progress.finish();
            })
            .catch(()=> {
                              Toast.fire({
              type: 'error',
              title: 'Gagal Perbarui data!'
            })
            this.$Progress.fail();

            });
          },
          getResults(page = 1) {
            axios.get('api/catatanBeli?page=' + page)
              .then(response => {
                this.catatanBelis = response.data;
              });
          }, 
          updateCatatanBelis(){
            this.$Progress.start();
            this.form.put('api/catatanBeli/'+this.form.id)

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
          editModal(catatanBeli){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(catatanBeli);
          },
          deletecatatanBelis(id){
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
                      this.form.delete('api/catatanBeli/'+id).then(()=>{
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
          loadcatatanBelis(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/catatanBeli").then(({data}) => (this.catatanBelis = data));
            }
          },
          createCatatanBelis(){
            this.$Progress.start();
            this.form.post('api/catatanBeli')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'catatanBeli Berhasil Dibuat!'
            })
            this.$Progress.finish()

            })
            .catch(()=>{
              this.$Progress.fail();
            })
            

          }
        },
        mounted() {
            Fire.$on('searching',()=>{
              let query = this.$parent.search;
              axios.get('api/findCatatanBeli?q=' + query)
              .then((data)=>{
                this.catatanBelis = data.data
              })
              .catch(()=>{

              });
            });
            this.loadcatatanBelis();
            Fire.$on('AfterCreated',() =>{
              this.loadcatatanBelis();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
