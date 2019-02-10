<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Ruangan</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Ruangan 
                    <i class="fas fa-door-open fa-fw"></i>
                </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Jumlah Kursi</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="ruangan in Ruangans.data" :key="ruangan.id">
                    <td>{{ruangan.id}}</td>
                    <td>{{ruangan.name}}</td>
                    <td>{{ruangan.jumlah_kursi }}</td>
                    <td>
                        <a href="#" @click="editModal(ruangan)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteRuangans(ruangan.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="Ruangans" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updateRuangans() : createRuangans()">
      <div class="modal-body">        
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Nama Ruangan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.jumlah_kursi" type="number" name="jumlah_kursi" placeholder="Jumlah Kursi" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah_kursi') }">
      <has-error :form="form" field="jumlah_kursi"></has-error>
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
                Ruangans: {},
                form: new Form({
                    id: '',
                    name : '',
                    jumlah_kursi : ''
                })
            }
        },
        methods: {
          getResults(page = 1) {
            axios.get('api/ruangan?page=' + page)
              .then(response => {
                this.Ruangans = response.data;
              });
          }, 
          updateRuangans(){
            this.$Progress.start();
            this.form.put('api/ruangan/'+this.form.id)

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
          editModal(ruangan){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(ruangan);
          },
          deleteRuangans(id){
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
                      this.form.delete('api/ruangan/'+id).then(()=>{
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
          loadRuangans(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/ruangan").then(({data}) => (this.Ruangans = data));
            }
          },
          createRuangans(){
            this.$Progress.start();
            this.form.post('api/ruangan')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'Ruangan Berhasil Dibuat!'
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
              axios.get('api/findRuangan?q=' + query)
              .then((data)=>{
                this.Ruangans = data.data
              })
              .catch(()=>{

              });
            });
            this.loadRuangans();
            Fire.$on('AfterCreated',() =>{
              this.loadRuangans();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
