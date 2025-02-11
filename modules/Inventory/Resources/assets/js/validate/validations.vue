<template>
    <el-dialog
        :visible="showDialog"
        title="Validaciones"
        width="40%"
        @close="close"
        @open="open"
        :lock-scroll="false"
        append-to-body
        v-loading="loading"
    >
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-end">Fecha</th>
                        <th class="text-end">Usuario</th>
                        <th class="text-end">Observaciones</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(validation,idx) in validations" :key="validation.id">
                        <td>{{ idx+1 }}</td>
                        <td class="text-end">
                            {{ validation.date_of_validation }}
                        </td>
                        <td class="text-end">{{ validation.user }}</td>
                        <td class="text-end">{{ validation.observations }}</td>
                        <td class="text-end">
                            <a
                                target="_blank"
                                :href="`/inventory/validate/report-validation/${validation.id}`"
                            >
                                Ver
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cerrar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "recordId"],
    data() {
        return {
            loading: false,
            validations: [],
        };
    },
    methods: {
        open() {
            // this.$emit("update:showDialog", true);
            this.getValidations();
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        async getValidations() {
            this.loading = true;
            this.$http
                .get(`/inventory/validate/validations/${this.recordId}`)
                .then((response) => {
                    this.validations = response.data.validations;
                    console.log(
                        "🚀 ~ this.$http.get ~ this.validations:",
                        this.validations
                    );
                })
                .catch((error) => {
                    console.log("🚀 ~ this.$http.get ~ error", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>

<style scoped>
.table-wrapper {
    max-height: 400px; /* Puedes ajustar esta altura según sea necesario */
    overflow-y: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    position: sticky;
    top: 0;
    background-color: #fff; /* Fondo blanco para que las cabeceras no se mezclen con el contenido */
    z-index: 1;
}

th,
td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.text-end {
    text-align: right;
}

.dialog-footer {
    text-align: right;
}
</style>
