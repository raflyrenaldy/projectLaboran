<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdminOrUser()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Peminjaman Inventory</h3>

                <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Tambah Data Peminjaman
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
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="peminjamanInventory in peminjamanInventories.data" :key="peminjamanInventory.id">
                    <td>{{peminjamanInventory.id}}</td>
                    <td>{{peminjamanInventory.get_user.name}}</td>
                    <td>{{peminjamanInventory.get_user_pinjam.name }}</td>
                    <td>{{peminjamanInventory.get_inventory.name }}</td>
                    <td>{{peminjamanInventory.jumlah }}</td>
                    <td>{{peminjamanInventory.keterangan }}</td>
                    <td>
                      <span v-if="peminjamanInventory.status == 'Dipinjam'" class="badge badge-warning">{{peminjamanInventory.status }}</span>
                      <span v-if="peminjamanInventory.status == 'Diminta'" class="badge badge-primary">{{peminjamanInventory.status }}</span>
                       <span v-if="peminjamanInventory.status == 'Sudah Dikembalikan'" class="badge badge-info">{{peminjamanInventory.status }}</span>
                    </td>
                    <td>
                        <a href="#" @click="editModal(peminjamanInventory)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deletepeminjamanInventories(peminjamanInventory.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                         /
                        <a v-if="peminjamanInventory.status == 'Sudah Dikembalikan'">
                            <i v-if="peminjamanInventory.status == 'Sudah Dikembalikan'" class="fa fa-check green"></i>
                            </a>
                        <a v-if="peminjamanInventory.status == 'Diminta'">
                            <i v-if="peminjamanInventory.status == 'Diminta'" class="fa fa-check green"></i>
                            </a>
                        <a href="#" v-if="peminjamanInventory.status == 'Dipinjam'" @click="selesaipeminjamanInventory(peminjamanInventory.id)">
                            <i v-if="peminjamanInventory.status == 'Dipinjam'" class="fa fa-check red"></i>
                        </a>
                    </td>
                  </tr>                 
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="peminjamanInventories" @pagination-change-page="getResults"></pagination>
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
      <form @submit.prevent="editMode ? updatePeminjamanInventories() : createPeminjamanInventories()">
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
            <select name="id_inventory" v-model="form.id_inventory" id="id_inventory" class="form-control" :class="{ 'is-invalid': form.errors.has('id_inventory') }">
            <option value="">Pilih Barang</option>
            <option v-for="inventory in inventories" v-bind:value="inventory.id">
                {{ inventory.name }}
            </option>
            </select>
            <has-error :form="form" field="id_inventory"></has-error>
     </div>
     <div class="form-group">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="Alasan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
    </div>

     <div class="form-group">
      <input v-model="form.jumlah" min="0" type="number" name="jumlah" placeholder="Jumlah" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah') }">
      <has-error :form="form" field="jumlah"></has-error>
    </div>
    <div class="form-group">
        <p-radio v-model="form.status" v-bind:value="form.status" name="status" class="p-default p-curve" :class="{ 'is-invalid': form.errors.has('jumlah') }" value="Dipinjam" color="info-o">Pinjam</p-radio>
        <p-radio v-model="form.status" v-bind:value="form.status" name="status" class="p-default p-curve" :class="{ 'is-invalid': form.errors.has('jumlah') }" value="Diminta" color="info-o">Minta</p-radio>
         <p-radio v-model="form.status" v-bind:value="form.status" name="status" class="p-default p-curve" :class="{ 'is-invalid': form.errors.has('jumlah') }" value="Sudah Dikembalikan" color="info-o">Sudah Dikembalikan</p-radio>
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
                inventories: [],
                peminjamanInventories: {},
                form: new Form({
                    id: '',
                    id_user: '',
                    id_user_pinjam: '',
                    id_inventory: '',
                    keterangan : '',
                    jumlah : '',
                    status : ''
                })
            }
        },
        methods: {
            selesaipeminjamanInventory(id){
            this.$Progress.start();
            this.form.get('api/peminjamanInventory/finish/'+id)
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
            axios.get('api/peminjamanInventory?page=' + page)
              .then(response => {
                this.peminjamanInventories = response.data;
              });
          }, 
          updatePeminjamanInventories(){
            this.$Progress.start();
            this.form.put('api/peminjamanInventory/'+this.form.id)

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
          loadInventory(){
              axios.get("api/app/inventory").then(({data}) => (this.inventories = data));
          },
          newModal(){
            this.editMode = false;
            this.form.reset();
            $('#addNew').modal('show');
            
          },
          editModal(peminjamanInventory){
            this.editMode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(peminjamanInventory);
          },
          deletepeminjamanInventories(id){
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
                      this.form.delete('api/peminjamanInventory/'+id).then(()=>{
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
          loadpeminjamanInventories(){
           if(this.$gate.isAdminOrUser()){
            axios.get("api/peminjamanInventory").then(({data}) => (this.peminjamanInventories = data));
            }
          },
          createPeminjamanInventories(){
            this.$Progress.start();
            this.form.post('api/peminjamanInventory')
            .then(()=>{
            Fire.$emit('AfterCreated');
            $('#addNew').modal('hide')

            Toast.fire({
              type: 'success',
              title: 'peminjamanInventory Berhasil Dibuat!'
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
              axios.get('api/findPeminjamanInventory?q=' + query)
              .then((data)=>{
                this.peminjamanInventories = data.data
              })
              .catch(()=>{

              });
            });
            this.loadInventory();
            this.loadUser();
            this.loadpeminjamanInventories();
            Fire.$on('AfterCreated',() =>{
              this.loadpeminjamanInventories();
            });
            // setInterval(() => this.loadUsers(),3000);
        }
    }
</script>
