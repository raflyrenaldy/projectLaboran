<style>
.widget-user-header{
    background-position: center center;
    background-size: cover;
    height: 200px !important;
}
.widget-user .card-footer{
    padding: 0;
}
</style>
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
               <div class="card">
        <div class="card-header">
          <h3 class="card-title">Detail Barang Hilang {{this.form.name}}</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="card-body">
          <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background-color: coral;">
                <h3 class="widget-user-username">{{this.form.name}}</h3>
                <h5 class="widget-user-desc">{{this.form.type}}</h5>
              </div>
              <div class="widget-user-image">
                <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
              </div>
            
            </div>
             <a class="btn btn-app" @click="changeEditMode" v-if="!editMode">
                  <i class="fa fa-edit" ></i> Edit
                </a>               
             
          <div class="row">
             
            <div class="col-md-2" v-show="!editMode">
              Nama Barang<br>
              Petugas Input<br>
              Ruangan<br>
              Keterangan<br>
              Waktu Penemuan<br>
              Waktu Diambil<br>
              Status<br>
              NPM<br>
              Nama Mahasiswa<br>
              No HP
            </div>
            <div class="col-md-10" v-show="!editMode">
              : {{this.form.name}}<br>
              : {{this.form.get_user.name}}<br>
              : {{this.form.get_ruangan.name}}<br>
              : {{this.form.keterangan}}<br>
              : {{this.form.waktu_penemuan | myDateTime}}<br>
              : {{this.form.waktu_diambil | myDateTime}}<br>
              : {{this.form.status}}<br>
              : {{this.form.npm}}<br>
              : {{this.form.name_mhs}}<br>
              : {{this.form.phone}}
            </div>
            <div class="col-md-12" v-show="editMode">
              <form>
              <div class="form-group row" >
       <label for="inputName2" class="col-sm-2 control-label">Nama Barang</label>
                <div class="col-sm-10">
      <input v-model="form.name" type="text" name="name" placeholder="Nama Barang" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
      <has-error :form="form" field="name"></has-error>
                </div>
    </div>
              <div class="form-group row" >
                <label for="inputName2" class="col-sm-2 control-label">Ruangan</label>
                <div class="col-sm-10">
            <select name="id_ruangan" v-model="form.id_ruangan" id="id_ruangan" class="form-control" :class="{ 'is-invalid': form.errors.has('id_ruangan') }">
            <option value="">Pilih Ruangan</option>
            <option v-for="ruangan in ruangans" v-bind:value="ruangan.id">
                {{ ruangan.name }}
            </option>
            </select>
            <has-error :form="form" field="id_ruangan"></has-error>
                        </div>
     </div>
     

     <div class="form-group row" >
       <label for="inputName2" class="col-sm-2 control-label">Keterangan</label>
                <div class="col-sm-10">
      <input v-model="form.keterangan" type="text" name="keterangan" placeholder="keterangan" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
      <has-error :form="form" field="keterangan"></has-error>
                </div>
    </div>
    <div class="form-group row" >
      <label for="inputName2" class="col-sm-2 control-label">Waktu Penemuan</label>
                <div class="col-sm-10">
      <datetime type="datetime" v-model="form.waktu_penemuan" format="yyyy-MM-dd HH:mm:ss"></datetime>
                </div>
    </div>
    <div class="form-group row" >
      <label for="inputName2" class="col-sm-2 control-label">Waktu Diambil</label>
                <div class="col-sm-10">
      <datetime type="datetime" v-model="form.waktu_diambil" format="yyyy-MM-dd HH:mm:ss"></datetime>
                </div>
    </div>
    <div class="form-group row" >
      <label for="inputName2" class="col-sm-2 control-label">NPM</label>
                <div class="col-sm-10">
      <input v-model="form.npm" type="text" name="npm" placeholder="NPM" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('npm') }">
      <has-error :form="form" field="npm"></has-error>
      </div>
    </div>
    <div class="form-group row" >
      <label for="inputName2" class="col-sm-2 control-label">No HP</label>
                <div class="col-sm-10">
      <input v-model="form.phone" type="number" name="phone" placeholder="No HP" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('phone') }">
      <has-error :form="form" field="phone"></has-error>
      </div>
    </div>
    <div class="form-group row" >
      <label for="inputName2" class="col-sm-2 control-label">Nama Mahasiswa</label>
                <div class="col-sm-10">
      <input v-model="form.name_mhs" type="text" name="name_mhs" placeholder="Nama Mahasiswa" 
        class="form-control" :class="{ 'is-invalid': form.errors.has('name_mhs') }">
      <has-error :form="form" field="name_mhs"></has-error>
      </div>
    </div>
    <div class="form-group row" >
      <label for="inputName2" class="col-sm-2 control-label">Foto</label>
                <div class="col-sm-10">
          <input name="foto" @change="updateProfile" type="file" class="form-control-file" :class="{ 'is-invalid': form.errors.has('foto') }" id="exampleFormControlFile1">
           <has-error :form="form" field="foto"></has-error>
           </div>
          </div>
          <a class="btn btn-app" @click="changeEditMode" v-if="editMode">
                  <i class="fa fa-edit" ></i> Cancel Edit
                </a>
             <a class="btn btn-app" v-show="editMode" @click="updateDetailBarang">
                  <i class="fa fa-save"></i> Save
                </a>
          </form>
          </div>
            </div>
          </div>
        </div>
      </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {     
        data(){
          return {
            editMode: false,
            ruangans: [],
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
                    get_user:[],
                    get_ruangan:[]
                })
          }
        },

        mounted() {
            Fire.$on('AfterCreated',() =>{
              this.created();
            });
        },

        methods:{
          changeEditMode(){
            if(this.editMode == false){
              this.editMode = true;
            }else{
              this.editMode = false;
            }
          },
          loadRuangan(){
              axios.get("/api/app/ruangan").then(({data}) => (this.ruangans = data));
          },
          getProfilePhoto(){
            let foto = (this.form.foto.length > 200) ? this.form.foto : "../img/fotomhs/"+ this.form.foto ;
            return foto;
          },
          updateDetailBarang(){
            this.$Progress.start();
            this.form.put("/api/barangHilang/"+this.form.id)
            .then(()=>{
              Fire.$emit('AfterCreate');
              Toast.fire({
              type: 'success',
              title: 'Barang Hilang Sudah diUpdate'
            })
            this.editMode = false;
              this.$Progress.finish();

            })
            .catch(()=>{
              this.$Progress.fail();

            });
          },
          updateProfile(e){
            // console.log('uploading file...');
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
            
          }

        },
         created() {
          axios.get("/api/barangHilang/"+ this.$route.params.id)
          .then(({ data }) => (this.form.fill(data)));
          this.loadRuangan();
        }
    }
   
</script>
