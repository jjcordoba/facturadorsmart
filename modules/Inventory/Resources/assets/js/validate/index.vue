<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>{{ title }}</span>
                </li>
            </ol>
        </div>
        <div class="card mb-0">
            <div class="card-header">
                <h3 class="my-0">Almacénes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Almacén</th>
                                <th>Última válidación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(warehouse, idx) in warehouses"
                                :key="idx"
                            >
                                <td>{{ warehouse.description }}</td>
                                <td>
                                    {{ warehouse.last_validation }}
                                    <template v-if="warehouse.id_validation">
                                        <a
                                            target="_blank"
                                            :href="`/inventory/validate/report-validation/${warehouse.id_validation}`"
                                        >
                                            Ver
                                        </a>
                                    </template>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-primary"
                                        v-if="warehouse.last_validation"
                                        @click="seeValidations(warehouse.id)"
                                    >
                                        Ver validaciones
                                    </button>
                                    <button
                                        class="btn btn-primary"
                                        @click="clickValidate(warehouse.id)"
                                    >
                                        Validar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <validate-form
                :showDialog.sync="showDialog"
                :warehouse_id="warehouse_id"
                :warehouse="warehouse"
            ></validate-form>
            <validations
                :showDialog.sync="showValidations"
                :recordId="currentId"
            ></validations>
        </div>
    </div>
</template>

<script>
import ValidateForm from "./form_validate.vue";
import Validations from "./validations.vue";
export default {
    components: {
        ValidateForm, Validations
    },
    props: ["type", "typeUser"],
    data() {
        return {
            title: null,
            showDialog: false,
            resource: "inventory/validate",
            warehouses: [],
            warehouse_id: null,
            warehouse: null,
            currentId: null,
            showValidations: false,
        };
    },
    created() {
        this.title = "Validar inventario";
        this.getTables();
    },
    methods: {
        seeValidations(id){
            this.currentId = id;
            this.showValidations = true;

        },
        clickValidate(id) {
            this.showDialog = true;
            this.warehouse_id = id;
            this.warehouse = this.warehouses.find(
                (warehouse) => warehouse.id === id
            );
        },
        getTables() {
            this.$http
                .get(`/${this.resource}/tables`)
                .then((response) => {
                    let data = response.data;
                    this.warehouses = data.warehouses;
                    console.log(
                        "🚀 ~ getTables ~ this.warehouses:",
                        this.warehouses
                    );
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
