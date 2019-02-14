<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Masalah Laboran</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Masalah Lab 
                    <i class="fas fa-exclamation-triangle fa-fw"></i>
                </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Ruangan</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Solusi Solved</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="masalahLab in masalahLabs.data" :key="masalahLab.id">
                    <td>{{masalahLab.id}}</td>
                    <td>{{masalahLab.name}}</td>
                    <td>{{masalahLab.ruangan }}</td>
                    <td>{{masalahLab.keterangan }}</td>
                    <td>
                      <span v-if="masalahLab.status == 'New'" class="badge badge-warning">{{masalahLab.status }}</span>
                      <span v-if="masalahLab.status == 'Proses'" class="badge badge-success">{{masalahLab.status }}</span>
                      <span v-if="masalahLab.status == 'Selesai'" class="badge badge-info">{{masalahLab.status }}</span>
                    </td>
                    <td>{{masalahLab.solusi_solved }}</td>
                    <td>
                        <a href="#" @click="editModal(masalahLab)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deletemasalahLabs(masalahLab.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                        /
                        <a v-if="masalahLab.status == 'Selesai'">
                            <i v-if="masalahLab.status == 'Selesai'" class="fas fa-check green"></i>
                            </a>
                          <a href="#" v-if="masalahLab.status == 'Proses'" @click="finishMasalah(masalahLab)">
                            <i v-if="masalahLab.status == 'Proses'" class="fas fa-play-circle blue"></i>
                            </a>
                        <a href="#" v-if="masalahLab.status == 'New'" @click="startMasalahLab(masalahLab.id)">
                            <i v-if="masalahLab.status == 'New'" class="fas fa-play-circle red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="masalahLabs" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updatemasalahLabs() : createmasalahLabs()">
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
    <div class="form-group" v-show="!editMode">
                 
     <p-check name="ruangan[]" v-model="form.ruangan" class="p-default p-curve p-thick p-smooth" :class="{ 'is-invalid': form.errors.has('ruangan') }" color="danger-o" v-for="ruangans in ruangan" v-bind:key="ruangans.id" v-bind:value="ruangans.id" >{{ ruangans.name }}</p-check>
    </div>
  
           
     <div class="form-group">
      <input v-model="form.name" type="text" name="name" placeholder="Nama Problem" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="Keterangan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
    </div>

    <div class="form-group" v-show="editMode" v-if="form.status == 'Selesai'">
      <textarea v-model="form.solusi_solved" type="text" name="solusi_solved" placeholder="Solusi Solved" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('solusi_solved') }">
      </textarea>
      <has-error :form="form" field="solusi_solved"></has-error>
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
<div class="modal fade" id="finishMasalahLab" tabindex="-1" role="dialog" aria-labelledby="finishMasalahLabLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="finishMasalahLabLabel">Masalah Selesai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="finishMasalahLab()">
      <div class="modal-body">     
           
     <div class="form-group">
      <textarea v-model="form.solusi_solved" type="text" name="solusi_solved" placeholder="Solusi Solved" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('solusi_solved') }">
      </textarea>
      <has-error :form="form" field="solusi_solved"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="Keterangan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
    </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Simpan</button>
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
                user: [],
                masalahLabs: {},
                form: new Form({
                    id: '',
                    id_user : '',
                    id_thnajaran : '',
                    name : '',
                    keterangan : '',
                    waktu_mulai : '',
                    waktu_selesai : '',
                    status : '',
                    yang_bertugas : '',
                    solusi_solved : '',
                    ruangan : []
                })
            }
        },
        methods: {
          finishMasalahLab(){
            this.$Progress.start();
            this.form.post('api/masalahLab/finish/'+this.form.id)
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#finishMasalahLab').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'masalah berhasil diselesaikan!'
            })
            this.$Progress.finish()

            })
            .catch(()=>{
              this.$Progress.fail();
            })            

          },
          finishMasalah(masalahLab){
            this.form.reset();
            $('#finishMasalahLab').modal('show');
            this.form.fill(masalahLab);

          },
          startMasalahLab(id){
            this.$Progress.start();
            this.form.get('api/masalahLab/start/'+id)
            .then(()=>{
               Fire.$emit('AfterCreated');
               
            Swal.fire(
                          'Selesai!',
                          'Mulai Memecahkan Masalah Lab!',
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
            axios.get('api/masalahLab?page=' + page)
              .then(response => {
                this.masalahLabs = response.data;
              });
          }, 
          updatemasalahLabs(){
            this.$Progress.start();
            this.form.put('api/masalahLab/'+this.form.id)

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
          editModal(masalahLab){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(masalahLab);
          },
          deletemasalahLabs(id){
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
                      this.form.delete('api/masalahLab/'+id).then(()=>{
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
          loadUser(){
              axios.get("api/app/user").then(({data}) => (this.user = data));
          },
          loadRuangan(){
              axios.get("api/app/ruangan").then(({data}) => (this.ruangan = data));
          },
          loadThnAjaran(){
              axios.get("api/app/thnajaran").then(({data}) => (this.tahunAjaran = data));
          },
          loadmasalahLabs(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/masalahLab").then(({data}) => (this.masalahLabs = data));
            }
          },
          createmasalahLabs(){
            this.$Progress.start();
            this.form.post('api/masalahLab')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'masalahLab Berhasil Dibuat!'
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
              axios.get('api/findMasalahLab?q=' + query)
              .then((data)=>{
                this.masalahLabs = data.data
              })
              .catch(()=>{

              });
            });
            this.loadmasalahLabs();
            this.loadUser();
            this.loadRuangan();
            this.loadThnAjaran();
            Fire.$on('AfterCreated',() =>{
              this.loadmasalahLabs();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
