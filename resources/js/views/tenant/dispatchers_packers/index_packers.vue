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
            <div class="right-wrapper pull-right">
                <button
                    type="button"
                    class="btn btn-custom btn-sm mt-2 mr-2"
                    @click.prevent="clickCreate()"
                >
                    <i class="fa fa-plus-circle"></i> Nuevo
                </button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Télefono</th>
                        <th class="text-end">Acciones</th>
                    </tr>

                    <tr></tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>
                            {{ row.name }}
                            <template v-if="row.identity_document_type_id">
                                <br />
                                <small class="text-mute">
                                    <strong>
                                        {{ row.identity_document_type }} :
                                    </strong>
                                    {{ row.number }}
                                </small>
                            </template>
                        </td>
                        <td>{{ row.telephone }}</td>
                        <td class="text-end">
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-sm btn-info"
                                @click.prevent="clickCreate(row.id)"
                            >
                                Editar
                            </button>
                            <template v-if="typeUser === 'admin'">
                                <button
                                    type="button"
                                    class="btn waves-effect waves-light btn-sm btn-danger"
                                    @click.prevent="clickDelete(row.id)"
                                >
                                    Eliminar
                                </button>
                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>

            <create-form
                :showDialog.sync="showDialog"
                :resource="resource"
                :recordId="recordId"
            ></create-form>
        </div>
    </div>
</template>

<script>
import CreateForm from "./form.vue";
import DataTable from "../../../components/BasicDataTable.vue";
import { deletable } from "../../../mixins/deletable";

export default {
    props: ["typeUser"],
    mixins: [deletable],
    components: { CreateForm, DataTable },
    data() {
        return {
            title: null,
            showDialog: false,
            showImportDialog: false,
            resource: "person-packers",
            recordId: null,
        };
    },
    created() {
        this.title = "Empaquetadores";
    },
    methods: {
        clickCreate(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        clickImport() {
            this.showImportDialog = true;
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
    },
};
</script>
