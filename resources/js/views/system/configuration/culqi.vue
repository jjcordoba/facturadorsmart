<template>
  <div class="card">
    <div class="card-header">
      <h3 class="my-0">Culqi</h3>
    </div>
    <div class="card-body">
      <form autocomplete="off" @submit.prevent="submit">
        <div class="form-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group" :class="{'has-danger': errors.token_public_culqui}">
                <label class="control-label">
                  Token Público
                  <el-tooltip placement="right-start">
                    <div slot="content">
                      Token Público.
                      <a href="#" @click="openCulqi">Culqi</a>
                    </div>
                    <i class="fa fa-info-circle"></i>
                  </el-tooltip>
                </label>
                <el-input v-model="form.token_public_culqui"></el-input>
                <small
                  class="text-danger"
                  v-if="errors.token_public_culqui"
                  v-text="errors.token_public_culqui[0]"
                ></small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group" :class="{'has-danger': errors.token_private_culqui}">
                <label class="control-label">
                  Token Privado
                  <el-tooltip placement="right-start">
                    <div slot="content">
                      Token Privado.
                      <a href="#" @click="openCulqi">Culqi</a>
                    </div>
                    <i class="fa fa-info-circle"></i>
                  </el-tooltip>
                </label>
                <el-input v-model="form.token_private_culqui"></el-input>
                <small
                  class="text-danger"
                  v-if="errors.token_private_culqui"
                  v-text="errors.token_private_culqui[0]"
                ></small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h3 class="my-0">Gekawa</h3>
            </div>
            <div class="col-md-12">
              <div class="form-group" :class="{'has-danger': errors.gekawa1}">
                <label class="control-label">Gekawa 1</label>
                <el-input v-model="form.gekawa1"></el-input>
                <small
                  class="text-danger"
                  v-if="errors.gekawa1"
                  v-text="errors.gekawa1[0]"
                ></small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group" :class="{'has-danger': errors.gekawa2}">
                <label class="control-label">Gekawa 2</label>
                <el-input v-model="form.gekawa2"></el-input>
                <small
                  class="text-danger"
                  v-if="errors.gekawa2"
                  v-text="errors.gekawa2[0]"
                ></small>
              </div>
            </div>
          </div>
        </div>
        <div class="form-actions text-end pt-2">
          <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading_submit: false,
      resource: "configurations",
      errors: {},
      form: {},
    };
  },
  async created() {
    await this.initForm();

    await this.$http.get(`/${this.resource}/record`).then(response => {
      this.form.token_public_culqui = response.data.token_public_culqui;
      this.form.token_private_culqui = response.data.token_private_culqui;
      this.form.gekawa1 = response.data.gekawa1;
      this.form.gekawa2 = response.data.gekawa2;
    });
  },
  methods: {
    openCulqi() {
      window.open("https://www.culqi.com");
    },
    initForm() {
      this.errors = {};
      this.form = {
        token_public_culqui: null,
        token_private_culqui: null,
        gekawa1: null,
        gekawa2: null,
      };
    },
    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        })
        .then(() => {
          this.loading_submit = false;
        });
    },
  }
};
</script>
