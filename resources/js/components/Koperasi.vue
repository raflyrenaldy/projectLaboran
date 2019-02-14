<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Koperasi Simpan Pinjam</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Data Koperasi
                    <i class="fas fa-hand-holding-usd fa-fw"></i>
                </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Petugas</th>
                    <th>Nama Peminjam</th>
                    <th>Alasan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="koperasi in koperasis.data" :key="koperasi.id">
                    <td>{{koperasi.id}}</td>
                    <td>{{koperasi.get_user.name}}</td>
                    <td>{{koperasi.get_user_pinjam.name }}</td>
                    <td>{{koperasi.keterangan }}</td>
                    <td>{{koperasi.jumlah }}</td>
                    <td>
                      <span v-if="koperasi.status == 'New'" class="badge badge-warning">{{koperasi.status }}</span>
                      <span v-if="koperasi.status == 'Sudah Di Bayar'" class="badge badge-info">{{koperasi.status }}</span>
                    </td>
                    <td>
                        <a href="#" @click="editModal(koperasi)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deletekoperasis(koperasi.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                         /
                        <a v-if="koperasi.status == 'Sudah Di Bayar'">
                            <i v-if="koperasi.status == 'Sudah Di Bayar'" class="fa fa-check green"></i>
                            </a>
                        <a href="#" v-if="koperasi.status == 'New'" @click="selesaiKoperasi(koperasi.id)">
                            <i v-if="koperasi.status == 'New'" class="fa fa-check red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="koperasis" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updateKoperasis() : createKoperasis()">
      <div class="modal-body">        
          <div class="form-group">
            <select name="id_user_pinjam" v-model="form.id_user_pinjam" id="id_user_pinjam" class="form-control" :class="{ 'is-invalid': form.errors.has('id_user_pinjam') }">
            <option value="">Pilih Pegawai</option>
            <option v-for="users in user" v-bind:value="users.id">
                {{ users.name }}
            </option>
            </select>
            <has-error :form="form" field="id_user_pinjam"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="Alasan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.jumlah" min="0" type="number" name="jumlah" placeholder="Jumlah dalam Rp" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah') }">
      <has-error :form="form" field="jumlah"></has-error>
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
                user: [],
                koperasis: {},
                form: new Form({
                    id: '',
                    id_user: '',
                    id_user_pinjam: '',
                    keterangan : '',
                    jumlah : '',
                    status : ''
                })
            }
        },
        methods: {
            selesaiKoperasi(id){
            this.$Progress.start();
            this.form.get('api/koperasi/finish/'+id)
            .then(()=>{
               Fire.$emit('AfterCreated');
               
            Swal.fire(
                          'Selesai!',
                          'Sudah Sudah Di Bayar!',
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
            axios.get('api/koperasi?page=' + page)
              .then(response => {
                this.koperasis = response.data;
              });
          }, 
          updateKoperasis(){
            this.$Progress.start();
            this.form.put('api/koperasi/'+this.form.id)

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
          loadUser(){
              axios.get("api/app/user").then(({data}) => (this.user = data));
          },
          newModal(){
            this.editMode = false;
            this.form.reset();
            $('#addNew').modal('show');
            
          },
          editModal(koperasi){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(koperasi);
          },
          deletekoperasis(id){
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
                      this.form.delete('api/koperasi/'+id).then(()=>{
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
          loadkoperasis(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/koperasi").then(({data}) => (this.koperasis = data));
            }
          },
          createKoperasis(){
            this.$Progress.start();
            this.form.post('api/koperasi')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'koperasi Berhasil Dibuat!'
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
              axios.get('api/findKoperasi?q=' + query)
              .then((data)=>{
                this.koperasis = data.data
              })
              .catch(()=>{

              });
            });
            this.loadUser();
            this.loadkoperasis();
            Fire.$on('AfterCreated',() =>{
              this.loadkoperasis();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
