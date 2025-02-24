<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Ingresos diversos</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a
                    :href="`/${resource}/create`"
                    class="btn btn-custom btn-sm mt-2 mr-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body">
                <data-table :resource="resource" ref="dataTable">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Fecha Emisión</th>
                        <th>Cliente</th>
                        <th>Número</th>
                        <th>Motivo</th>
                        <!-- <th class="text-center">Pagos</th> -->
                        <th class="text-center">Moneda</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Dist. Ingreso</th>
                    </tr>

                    <tr></tr>
                    <tr
                        slot-scope="{ index, row }"
                        :class="{
                            'text-danger': row.state_type_id === '11',
                            'text-warning': row.state_type_id === '13',
                            'border-light': row.state_type_id === '01',
                            'border-left border-info':
                                row.state_type_id === '03',
                            'border-left border-success':
                                row.state_type_id === '05',
                            'border-left border-secondary':
                                row.state_type_id === '07',
                            'border-left border-dark':
                                row.state_type_id === '09',
                            'border-left border-danger':
                                row.state_type_id === '11',
                            'border-left border-warning':
                                row.state_type_id === '13',
                        }"
                    >
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td>{{ row.customer_name }}</td>
                        <td>
                            {{ row.number }}<br />
                            <small v-text="row.income_type_description"></small
                            ><br />
                        </td>
                        <td class="">{{ row.income_reason_description }}</td>
                        <!-- <td class="text-center">
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-info m-1__2"
                                @click.prevent="clickExpensePayment(row.id)"
                            >Pagos</button>
                        </td> -->
                        <td class="text-center">{{ row.currency_type_id }}</td>
                        <td class="text-end">{{ row.total }}</td>

                        <td class="text-center">
                            <el-tooltip content="Duplicar gasto">
                                <button
                                    type="button"
                                    style="min-width: 41px"
                                    class="btn waves-effect waves-light btn-sm btn-warning m-1__2"
                                    @click.prevent="clickDuplicate(row.id)"
                                >
                                    <i class="fas fa-copy"></i>
                                </button>
                            </el-tooltip>
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-primary m-1__2"
                                @click.prevent="clickPrint(row.external_id)"
                            >
                                <i class="fas fa-print"></i>
                            </button>
                            <button
                                type="button"
                                v-if="row.state_type_id != '11'"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-primary m-1__2"
                                @click.prevent="clickCreate(row.id)"
                            >
                                <i class="fa fa-pen"></i>
                            </button>
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-info m-1__2"
                                @click.prevent="clickPayment(row.id)"
                            >
                                <i class="fa fa-search"></i>
                            </button>

                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-danger m-1__2"
                                @click.prevent="clickVoided(row.id)"
                                v-if="row.state_type_id === '05'"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <income-payments
                :showDialog.sync="showDialogPayments"
                :recordId="recordId"
            ></income-payments>
        </div>
    </div>
</template>

<script>
import DataTable from "@components/DataTableResource.vue";
import { deletable } from "@mixins/deletable";
import IncomePayments from "./partials/payments.vue";

export default {
    mixins: [deletable],
    components: { DataTable, IncomePayments },
    data() {
        return {
            showDialogVoided: false,
            resource: "finances/income",
            showDialogPayments: false,
            recordId: null,
            showDialogOptions: false,
        };
    },
    created() {},
    methods: {
        async clickDuplicate(id) {
            const response = await this.$http.get(
                `/${this.resource}/duplicate/${id}`
            );
            if (response.status === 200) {
                if (response.data.success) {
                    this.$message({
                        type: "success",
                        message: response.data.message,
                    });
                    this.$refs.dataTable.getRecords();
                } else {
                    this.$message({
                        type: "error",
                        message: "Ocurrió un error al duplicar el registro.",
                    });
                }
            } else {
                this.$message({
                    type: "error",
                    message: "Ocurrió un error al duplicar el registro.",
                });
            }
        },
        clickCreate(id = "") {
            location.href = `/${this.resource}/create/${id}`;
        },
        clickPrint(external_id) {
            window.open(`/${this.resource}/print/${external_id}`, "_blank");
        },
        clickExpensePayment(recordId) {
            this.recordId = recordId;
            this.showDialogExpensePayments = true;
        },
        clickVoided(recordId) {
            this.voided(`/${this.resource}/voided/${recordId}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickDownload(download) {
            window.open(download, "_blank");
        },
        clickOptions(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptions = true;
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
    },
};
</script>
