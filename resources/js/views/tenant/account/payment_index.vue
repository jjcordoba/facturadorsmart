<template>
  <div>
    <div class="page-header pr-0">
      <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
      <ol class="breadcrumbs">
          <li class="active"><span>Pagos</span></li>
      </ol>
    </div>
    <div class="card">
      <div class="card-header">
        <h3 class="my-0">Listado de Pagos</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col"></div>
        </div>
        <div v-if="alertMessage" class="alert" :class="{'alert-success': alertType === 'success', 'alert-danger': alertType === 'error'}">
          {{ alertMessage }}
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr width="100%">
                <th width="5%">#</th>
                <th>Fecha de pago</th>
                <th>Fecha real de pago</th>
                <th>Comentario</th>
                <th class="text-center">Monto</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Subir pago</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in records" :key="row.id">
                <td>{{ index + 1 }}</td>
                <td>{{ row.date_of_payment }}</td>
                <td>{{ row.date_of_payment_real }}</td>
                <td>{{ row.comentario }}</td>
                <td class="text-center">{{ row.payment }}</td>
                <td class="text-center">{{ row.state_description }}</td>
                <td class="text-center">
                  <div v-if="row.photos && row.photos.length">
                    <img v-for="(photo, idx) in row.photos" :key="idx" :src="`/storage/${photo}`" alt="photo" style="width: 50px; height: 50px; margin-right: 5px;">
                  </div>
                  <input type="file" :ref="'fileInput' + row.id" style="display: none;" @change="uploadFiles($event, row.id)" multiple>
                  <button type="button" class="btn btn-custom btn-sm mt-2" @click="triggerFileInput(row.id)">Subir Imagen</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showDialog: false,
      resource: "cuenta",
      recordId: null,
      records: [],
      culqi_: {},
      alertMessage: '',
      alertType: ''
    };
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.$http.get(`/${this.resource}/payment_records`).then(response => {
        this.records = response.data.data;
      });
    },
    clickPayment(id, payment) {
      window.execCulqi(id, payment);
    },
    triggerFileInput(paymentId) {
      this.$refs['fileInput' + paymentId][0].click();
    },
    uploadFiles(event, paymentId) {
      const files = event.target.files;
      if (files.length > 0) {
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
          formData.append('files[]', files[i]);
        }
        formData.append('paymentId', paymentId);

        this.$http.post(`/${this.resource}/upload_payment_files`, formData)
          .then(response => {
            console.log(response.data.message);
            this.getData(); // Refrescar la lista de pagos para mostrar las nuevas fotos
            this.showAlert('Imagen subida exitosamente.', 'success');
          })
          .catch(error => {
            console.error('Error al subir los archivos:', error);
            this.showAlert('Error al subir la imagen.', 'error');
          });
      }
    },
    showAlert(message, type) {
      this.alertMessage = message;
      this.alertType = type;
      setTimeout(() => {
        this.alertMessage = '';
        this.alertType = '';
      }, 3000); // Ocultar la alerta después de 3 segundos
    }
  }
};
</script>

<style>
.alert {
  padding: 10px;
  margin-top: 10px;
}
.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>
