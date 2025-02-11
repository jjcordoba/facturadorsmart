<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Botellas devueltas</span></li>
            </ol>
            
        </div>
        <div class="card mb-0" >
            
            <div class="card-body">
                <data-table :resource="resource" :state-types="state_types">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Fecha emisión</th>
                        <th>Documento</th>
                        <th>Serie</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Devuelto</th>
                    
                    </tr>

                    <tr></tr>
                    <tr
                        slot-scope="{ index, row }"
                        :class="{ anulate_color: row.state_type_id == '11' }"
                    >
                        <td>{{ index }}</td>
                        <td>{{ row.date_of_issue }}</td>
                        <td>{{ row.document_type }}</td>
                        <td>{{ row.series }}</td>
                        <td>{{ row.number }}</td>
                        <td>{{ row.customer_name }}</td>
                        <td>{{ row.quantity }}</td>
                    </tr>
                </data-table>
            </div>
        </div>
    </div>
</template>
<style scoped>
.anulate_color {
    color: red;
}
</style>
<script>
import DataTable from "../../../components/DataTable.vue";
import { deletable } from "../../../mixins/deletable";
import { mapActions, mapState } from "vuex";

export default {
    props: [
        "isCommercial",
        "typeUser",
        "soapCompany",
        "generateOrderNoteFromQuotation",
        "isIntegrateSystem",
    ],
    mixins: [deletable],
    components: {
        DataTable,
    },
    computed: {
        ...mapState(["config"]),
    },
    data() {
        return {
            resource: "warranty_document/report",
            showDialogSendEmailDocument: false,
            recordId: null,
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogOptionsPdf: false,
            state_types: [],
            loading:true,
            columns: {
                total_exportation: {
                    title: "T.Exportación",
                    visible: false,
                },
                total_unaffected: {
                    title: "T.Inafecto",
                    visible: false,
                },
                total_exonerated: {
                    title: "T.Exonerado",
                    visible: false,
                },
                total_free: {
                    title: "T.Gratuito",
                    visible: false,
                },
                contract: {
                    title: "Contrato",
                    visible: false,
                },
                delivery_date: {
                    title: "T.Entrega",
                    visible: false,
                },
                referential_information: {
                    title: "Inf.Referencial",
                    visible: false,
                },
                order_note: {
                    title: "Pedidos",
                    visible: false,
                },
                exchange_rate_sale: {
                    title: "Tipo de cambio",
                    visible: false,
                },
            },
        };
    },
    async created() {
        console.log("is commercial: ", this.isCommercial);
        await this.filter();
    },
    mounted() {
        this.loadConfiguration();
    },
    methods: {
        clickReturn(id) {
            this.$confirm("¿Está seguro de devolver el documento?", "Advertencia", {
                confirmButtonText: "Sí",
                cancelButtonText: "No",
                type: "warning",
            })
                .then(() => {
                    this.loading = true;
                    this.$http
                .get(`/warranty_document/return_warranty/${id}`)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success("Se devolvió correctamente.");
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se pudo devolver.");
                    }
                }).catch((error) => {
                    console.log("error: ", error);
                }).finally(() => {
                    this.loading = false;
                });

                })
                .catch(() => {});
        },
        clickSendQuotation(id) {
            this.recordId = id;
            this.showDialogSendEmailDocument = true;
        },
        ...mapActions(["loadConfiguration"]),
        canMakeOrderNote(row) {
            let permission = true;

            // Si ya tiene Pedidos, no se genera uno nuevo
            if (row.order_note.full_number) {
                permission = false;
            } else {
                if (this.typeUser !== "admin") {
                    permission = this.generateOrderNoteFromQuotation;
                }
            }

            return permission;
        },
        clickPrintContract(external_id) {
            window.open(`/contracts/print/${external_id}/a4`, "_blank");
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        async changeStateType(row) {
            await this.updateStateType(
                `/${this.resource}/state-type/${row.state_type_id}/${row.id}`
            ).then(() => this.$eventHub.$emit("reloadData"));
        },
        async filter() {
            await this.$http
                .get(`/${this.resource}/filter`)
                .then((response) => {
                    this.state_types = response.data.state_types;
                });
        },
        clickEdit(id) {
            this.recordId = id;
            this.showDialogFormEdit = true;
        },
        clickOptions(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptions = true;
        },
        clickOptionsPdf(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptionsPdf = true;
        },
        clickAnulate(id) {
            this.anular(`/${this.resource}/anular/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        makeOrder(quotation) {
            let tos = parseInt(quotation);
            localStorage.setItem("Quotation", tos);
            localStorage.setItem("FromQuotation", true);
            window.location.href = "/order-notes/create";
        },
        duplicate(id) {
            this.$http
                .post(`${this.resource}/duplicate`, { id })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(
                            "Se guardaron los cambios correctamente."
                        );
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch((error) => {});
            this.$eventHub.$emit("reloadData");
        },
        clickGenerateDocument(recordId) {
            window.location.href = `/documents/create/quotations/${recordId}`;
        },
    },
};
</script>