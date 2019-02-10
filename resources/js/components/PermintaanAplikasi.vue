<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Permintaan Aplikasi</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Permintaan Aplikasi 
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
                    <th>Tahun Ajaran</th>
                    <th>Ruangan</th>
                    <th>Nama Aplikasi</th>
                    <th>Nama Dosen</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="permintaanAplikasi in permintaanAplikasis.data" :key="permintaanAplikasi.id">
                    <td>{{permintaanAplikasi.id}}</td>
                    <td>{{permintaanAplikasi.get_user.name}}</td>
                    <td>{{permintaanAplikasi.get_thnajaran.name}}</td>
                    <td>{{permintaanAplikasi.get_ruangan.name}}</td>
                    <td>{{permintaanAplikasi.name }}</td>
                    <td>{{permintaanAplikasi.name_dosen }}</td>
                    <td>{{permintaanAplikasi.status }}</td>
                    <td>{{permintaanAplikasi.deadline | myDate }}</td>
                    <td>
                        <a href="#" @click="editModal(permintaanAplikasi)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deletepermintaanAplikasis(permintaanAplikasi.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="permintaanAplikasis" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updatepermintaanAplikasis() : createpermintaanAplikasis()">
      <div class="modal-body"> 
    <div class="form-group">
            <select name="id_thnajaran" v-model="form.id_thnajaran" id="id_thnajaran" class="form-control" :class="{ 'is-invalid': form.errors.has('id_thnajaran') }">
            <option value="">Pilih Tahun Ajaran</option>
            <option v-for="thnajaran in tahunAjaran" v-bind:value="thnajaran.id">
                {{ thnajaran.name }}
            </option>
            </select>
            <has-error :form="form" field="id_thnajaran"></has-error>
    </div>
    <div class="form-group">
                 
     <p-check name="id_ruangan[]" v-model="form.id_ruangan" class="p-default p-curve p-thick p-smooth" :class="{ 'is-invalid': form.errors.has('id_ruangan') }" color="danger-o" v-for="ruangans in ruangan" v-bind:key="ruangans.id" v-bind:value="ruangans.id">{{ ruangans.name }}</p-check>
    </div>
  
           
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Nama Aplikasi" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.name_dosen" type="text" name="name_dosen" placeholder="Nama Dosen" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name_dosen') }">
      <has-error :form="form" field="name_dosen"></has-error>
    </div>
    <div class="form-group">
      <input v-model="form.deadline" type="date" name="deadline" placeholder="deadline" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('deadline') }">
      <has-error :form="form" field="deadline"></has-error>
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
                ruangan: [],
                tahunAjaran: [],
                permintaanAplikasis: {},
                form: new Form({
                    id: '',
                    id_user : '',
                    id_ruangan : [],
                    id_thnajaran : '',
                    name : '',
                    name_dosen : '',
                    status : '',
                    deadline : ''
                })
            }
        },
        methods: {
          getResults(page = 1) {
            axios.get('api/permintaanAplikasi?page=' + page)
              .then(response => {
                this.permintaanAplikasis = response.data;
              });
          }, 
          updatepermintaanAplikasis(){
            this.$Progress.start();
            this.form.put('api/permintaanAplikasi/'+this.form.id)

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
          editModal(permintaanAplikasi){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(permintaanAplikasi);
          },
          deletepermintaanAplikasis(id){
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
                      this.form.delete('api/permintaanAplikasi/'+id).then(()=>{
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
          loadRuangan(){
              axios.get("api/app/ruangan").then(({data}) => (this.ruangan = data));
          },
          loadThnAjaran(){
              axios.get("api/app/thnajaran").then(({data}) => (this.tahunAjaran = data));
          },
          loadpermintaanAplikasis(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/permintaanAplikasi").then(({data}) => (this.permintaanAplikasis = data));
            }
          },
          createpermintaanAplikasis(){
            this.$Progress.start();
            this.form.post('api/permintaanAplikasi')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'permintaanAplikasi Berhasil Dibuat!'
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
              axios.get('api/findpermintaanAplikasi?q=' + query)
              .then((data)=>{
                this.permintaanAplikasis = data.data
              })
              .catch(()=>{

              });
            });
            this.loadpermintaanAplikasis();
            this.loadRuangan();
            this.loadThnAjaran();
            Fire.$on('AfterCreated',() =>{
              this.loadpermintaanAplikasis();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
