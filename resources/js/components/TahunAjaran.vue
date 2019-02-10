<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Tahun Ajaran</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Tahun Ajaran 
                    <i class="fas fa-calendar-plus fa-fw"></i>
                </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Berakhir</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="thnAjaran in tahunAjaran.data" :key="thnAjaran.id">
                    <td>{{thnAjaran.id}}</td>
                    <td>{{thnAjaran.name}}</td>
                    <td>{{thnAjaran.waktu_mulai | myDate}}</td>
                    <td>{{thnAjaran.waktu_berakhir | myDate }}</td>
                    <td>
                        <a href="#" @click="editModal(thnAjaran)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteTahunAjaran(thnAjaran.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="tahunAjaran" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updateTahunAjaran() : createTahunAJaran()">
      <div class="modal-body">        
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Name" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.waktu_mulai" type="date" name="waktu_mulai" placeholder="Waktu Mulai" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('waktu_mulai') }">
      <has-error :form="form" field="waktu_mulai"></has-error>
    </div>
    <div class="form-group">
      <input v-model="form.waktu_berakhir" type="date" name="waktu_berakhir" placeholder="Waktu Berakhir" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('waktu_berakhir') }">
      <has-error :form="form" field="waktu_berakhir"></has-error>
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
                tahunAjaran: {},
                form: new Form({
                    id: '',
                    name : '',
                    waktu_mulai : '',
                    waktu_berakhir : ''
                })
            }
        },
        methods: {
          getResults(page = 1) {
            axios.get('api/tahunAjaran?page=' + page)
              .then(response => {
                this.tahunAjaran = response.data;
              });
          }, 
          updateTahunAjaran(){
            this.$Progress.start();
            this.form.put('api/tahunAjaran/'+this.form.id)

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
          editModal(thnAjaran){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(thnAjaran);
          },
          deleteTahunAjaran(id){
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
                      this.form.delete('api/tahunAjaran/'+id).then(()=>{
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
          loadTahunAjaran(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/tahunAjaran").then(({data}) => (this.tahunAjaran = data));
            }
          },
          createTahunAJaran(){
            this.$Progress.start();
            this.form.post('api/tahunAjaran')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'Tahun Ajaran Berhasil Dibuat!'
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
              axios.get('api/findThnAjaran?q=' + query)
              .then((data)=>{
                this.tahunAjaran = data.data
              })
              .catch(()=>{

              });
            });
            this.loadTahunAjaran();
            Fire.$on('AfterCreated',() =>{
              this.loadTahunAjaran();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
