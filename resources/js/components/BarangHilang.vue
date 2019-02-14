<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Barang Hilang</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Data Barang Hilang
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
                    <th>Ruangan</th>
                    <th>Nama Barang</th>
                    <th>Keterangan</th>
                    <th>Waktu Penemuan</th>
                    <th>Status</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="barangHilang in barangHilangs.data" :key="barangHilang.id">
                    <td>{{barangHilang.id}}</td>
                    <td>{{barangHilang.get_user.name}}</td>
                    <td>{{barangHilang.get_ruangan.name }}</td>
                    <td>{{barangHilang.name }}</td>
                    <td>{{barangHilang.keterangan }}</td>
                    <td>{{barangHilang.waktu_penemuan | myDateTime}}</td>
                    <td>
                      <span v-if="barangHilang.status == 'New'" class="badge badge-warning">{{barangHilang.status }}</span>
                       <span v-if="barangHilang.status == 'Sudah Diambil'" class="badge badge-info">{{barangHilang.status }}</span>
                    </td>
                    <td>
                        <a href="#" @click="editModal(barangHilang)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteBarangHilangs(barangHilang.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                         /
                        <router-link :to="{ path: '/barang-hilang/'+ barangHilang.id}" v-if="barangHilang.status == 'Sudah Diambil'">
                            <i v-if="barangHilang.status == 'Sudah Diambil'" class="fa fa-check green"></i>
                            </router-link>
                        <a href="#" v-if="barangHilang.status == 'New'" @click="diambilModal(barangHilang)">
                            <i v-if="barangHilang.status == 'New'" class="fa fa-check red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="barangHilangs" @pagination-change-page="getResults"></pagination>
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
        <h5 v-show="!editMode && !diambil" class="modal-title" id="addNewLabel">Tambah Data</h5>
        <h5 v-show="editMode" class="modal-title" id="addNewLabel">Edit Data</h5>
        <h5 v-show="diambil" class="modal-title" id="addNewLabel">Data Hilang Diambil</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editMode ? updateBarangHilangs() : createBarangHilangs()">
      <div class="modal-body">       
     <div class="form-group">
            <select name="id_ruangan" v-model="form.id_ruangan" id="id_ruangan" class="form-control" :class="{ 'is-invalid': form.errors.has('id_ruangan') }">
            <option value="">Pilih Barang</option>
            <option v-for="ruangan in ruangans" v-bind:value="ruangan.id">
                {{ ruangan.name }}
            </option>
            </select>
            <has-error :form="form" field="id_ruangan"></has-error>
     </div>
     <div class="form-group" v-show="!diambil">
      <input v-model="form.name" type="text" name="name" placeholder="Nama Barang" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="keterangan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
    </div>
    <div class="form-group" v-show="!diambil">
      <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="form.waktu_penemuan"></datetime>
    </div>
    <div class="form-group" v-show="diambil">
      <input v-model="form.npm" type="text" name="npm" placeholder="NPM" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('npm') }">
      <has-error :form="form" field="npm"></has-error>
    </div>
    <div class="form-group" v-show="diambil">
      <input v-model="form.phone" type="number" name="phone" placeholder="No HP" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('phone') }">
      <has-error :form="form" field="phone"></has-error>
    </div>
    <div class="form-group" v-show="diambil">
      <input v-model="form.name_mhs" type="text" name="name_mhs" placeholder="Nama Mahasiswa" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name_mhs') }">
      <has-error :form="form" field="name_mhs"></has-error>
    </div>
    <div class="form-group" v-show="diambil">
         <label for="inputName2" class="col-sm-2 control-label">Foto</label>
      <div class="col-sm-10">
       <input name="foto" type="file" @change="fotoMhs" class="form-control-file" :class="{ 'is-invalid': form.errors.has('foto') }" id="exampleFormControlFile1">
       <has-error :form="form" field="foto"></has-error>
      </div>
    </div>

    
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button v-show="editMode" type="submit" class="btn btn-success">Simpan</button>
        <button v-show="!editMode && !diambil" type="submit" class="btn btn-primary">Tambah Baru</button>
        <button v-show="diambil" type="submit" class="btn btn-primary">Diambil</button>
      </div>
    </form>

    </div>
  </div>
</div>
    </div>
</template>

<script>
import datetime from 'vuejs-datetimepicker';

    export default {
        components: {
           datetime 
        },
        data() {
            return{
                diambil: false,
                editMode: false,
                ruangans: [],
                barangHilangs: {},
                form: new Form({
                    id: '',
                    id_user: '',
                    id_ruangan: '',
                    name: '',
                    keterangan : '',
                    waktu_penemuan : '',
                    waktu_diambil : '',
                    status : '',
                    npm : '',
                    phone : '',
                    foto : '',
                    name_mhs : '',
                })
            }
        },
        methods: {
         fotoMhs(e){
            // cons          fotoMhs"ole.log('uploading file...');
            let file = e.target.files[0];
            let reader = new FileReader();

            if(file['size'] < 2111775){
              reader.onloadend = (file) => {
              // console.log('RESULT', reader.result)
              this.form.foto = reader.result;
              }
              reader.readAsDataURL(file);
            }else{
              Swal({
                    type: 'error',
                    title: 'Oops...',
                    text: 'You are uploading a large file',
              })
            }           
          },
            ambilBarangHilangs(){
            this.$Progress.start();
            this.form.put('api/barangHilang/finish/'+this.form.id)
            .then(()=>{
               Fire.$emit('AfterCreated');
            $('#addNew').modal('hide');
            Swal.fire(
                          'Selesai!',
                          'Sudah Di ambil!',
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
            axios.get('api/barangHilang?page=' + page)
              .then(response => {
                this.barangHilangs = response.data;
              });
          }, 
          updateBarangHilangs(){
            this.$Progress.start();
            this.form.put('api/barangHilang/'+this.form.id)

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
          loadRuangan(){
              axios.get("api/app/ruangan").then(({data}) => (this.ruangans = data));
          },
          newModal(){
            this.diambil = false;
            this.editMode = false;
            this.form.reset();
            $('#addNew').modal('show');
            
          },
          diambilModal(barangHilang){
            this.diambil = true;
            this.editMode = false;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(barangHilang);
          },
          editModal(barangHilang){
            this.diambil = false;
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(barangHilang);
          },
          deleteBarangHilangs(id){
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
                      this.form.delete('api/barangHilang/'+id).then(()=>{
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
          loadbarangHilangs(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/barangHilang").then(({data}) => (this.barangHilangs = data));
            }
          },
          createBarangHilangs(){
            if(this.diambil == true){
              this.ambilBarangHilangs();
            }else{
              this.$Progress.start();
            this.form.post('api/barangHilang')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'barangHilang Berhasil Dibuat!'
            })
            this.$Progress.finish()

            })
            .catch(()=>{
              this.$Progress.fail();
            })
            }
            
            

          }
        },
        mounted() {
            Fire.$on('searching',()=>{
              let query = this.$parent.search;
              axios.get('api/findBarangHilang?q=' + query)
              .then((data)=>{
                this.barangHilangs = data.data
              })
              .catch(()=>{

              });
            });
            this.loadRuangan();
            this.loadbarangHilangs();
            Fire.$on('AfterCreated',() =>{
              this.loadbarangHilangs();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
