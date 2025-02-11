<template>
    <el-dialog
        :title="title"
        :visible="showDialog"
        @close="close"
        @open="getData"
        width="65%"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
    >
        <div class="form-body">
            <div class="row">
                <div class="col-md-12" v-if="records.length > 0">
                    <div class="table-responsive table-sm">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha de pago</th>
                                    <th>Método de pago</th>
                                    <th>Destino</th>
                                    <th>Referencia</th>
                                    <th>Archivo</th>
                                    <th class="text-end">Monto</th>
                                    <template v-if="external">
                                        <th>Imprimir</th>
                                    </template>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, index) in records"
                                    :key="index"
                                >
                                    <template v-if="row.id">
                                        <td>PAGO-{{ row.id }}</td>
                                        <td>{{ row.date_of_payment }}</td>
                                        <td>
                                            {{
                                                row.payment_method_type_description
                                            }}
                                        </td>
                                        <td>
                                            {{ row.destination_description }}
                                        </td>
                                        <td>{{ row.reference }}</td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                v-if="row.filename"
                                                class="btn waves-effect waves-light btn-sm btn-primary"
                                                @click.prevent="
                                                    clickDownloadFile(
                                                        row.filename
                                                    )
                                                "
                                            >
                                                <i
                                                    class="fas fa-file-download"
                                                ></i>
                                            </button>
                                        </td>
                                        <td class="text-end">
                                            {{ row.payment }}
                                        </td>
                                        <template v-if="external">
                                            <td
                                                class="series-table-actions text-center"
                                            >
                                                <button
                                                    type="button"
                                                    class="btn waves-effect waves-light btn-sm btn-primary"
                                                    @click.prevent="
                                                        clickOptions()
                                                    "
                                                >
                                                    <i
                                                        class="fas fa-file-upload"
                                                    ></i>
                                                </button>
                                            </td>
                                        </template>

                                        <td
                                            class="series-table-actions text-end"
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <template
                                                    v-if="
                                                        !row.document_prepayment_id &&
                                                        configuration.emit_prepayment_document_from_sale_note
                                                    "
                                                >
                                                    <template
                                                        v-if="
                                                            records.length -
                                                                1 ==
                                                                index &&
                                                            document.total_difference ==
                                                                0
                                                        "
                                                    >
                                                        <el-tooltip
                                                            v-if="row.show"
                                                            content="Aplicar anticipos"
                                                            placement="top"
                                                            effect="dark"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="btn waves-effect waves-light btn-sm btn-info"
                                                                @click.prevent="
                                                                    clickDocumentPrePayment(
                                                                        index,
                                                                        true
                                                                    )
                                                                "
                                                            >
                                                                <i
                                                                    class="fa fa-file"
                                                                ></i>
                                                            </button>
                                                        </el-tooltip>
                                                    </template>
                                                    <template v-else>
                                                        <el-tooltip
                                                            v-if="row.show"
                                                            content="Emitir factura por anticipo del pago"
                                                            placement="top"
                                                            effect="dark"
                                                        >
                                                            <button
                                                                type="button"
                                                                class="btn waves-effect waves-light btn-sm btn-success"
                                                                @click.prevent="
                                                                    clickDocumentPrePayment(
                                                                        index
                                                                    )
                                                                "
                                                            >
                                                                <i
                                                                    class="fa fa-file"
                                                                ></i>
                                                            </button>
                                                        </el-tooltip>
                                                    </template>
                                                </template>
                                                <button
                                                    type="button"
                                                    class="ml-2 btn waves-effect waves-light btn-sm btn-danger"
                                                    @click.prevent="
                                                        clickDelete(row.id)
                                                    "
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <!--<el-button type="danger" icon="el-icon-delete" plain @click.prevent="clickDelete(row.id)"></el-button>-->
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td></td>
                                        <td>
                                            <div
                                                class="form-group mb-0"
                                                :class="{
                                                    'has-danger':
                                                        row.errors
                                                            .date_of_payment,
                                                }"
                                            >
                                                <el-date-picker
                                                    v-model="
                                                        row.date_of_payment
                                                    "
                                                    type="date"
                                                    :clearable="false"
                                                    format="dd/MM/yyyy"
                                                    value-format="yyyy-MM-dd"
                                                ></el-date-picker>
                                                <small
                                                    class="text-danger"
                                                    v-if="
                                                        row.errors
                                                            .date_of_payment
                                                    "
                                                    v-text="
                                                        row.errors
                                                            .date_of_payment[0]
                                                    "
                                                ></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="form-group mb-0"
                                                :class="{
                                                    'has-danger':
                                                        row.errors
                                                            .payment_method_type_id,
                                                }"
                                            >
                                                <el-select
                                                    v-model="
                                                        row.payment_method_type_id
                                                    "
                                                >
                                                    <el-option
                                                        v-for="option in payment_method_types"
                                                        v-show="
                                                            option.id != '09'
                                                        "
                                                        :key="option.id"
                                                        :value="option.id"
                                                        :label="
                                                            option.description
                                                        "
                                                    ></el-option>
                                                </el-select>
                                                <small
                                                    class="text-danger"
                                                    v-if="
                                                        row.errors
                                                            .payment_method_type_id
                                                    "
                                                    v-text="
                                                        row.errors
                                                            .payment_method_type_id[0]
                                                    "
                                                ></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="form-group mb-0"
                                                :class="{
                                                    'has-danger':
                                                        row.errors
                                                            .payment_destination_id,
                                                }"
                                            >
                                                <el-select
                                                    v-model="
                                                        row.payment_destination_id
                                                    "
                                                    filterable
                                                    :disabled="
                                                        row.payment_destination_disabled
                                                    "
                                                >
                                                    <el-option
                                                        v-for="option in payment_destinations"
                                                        :key="option.id"
                                                        :value="option.id"
                                                        :label="
                                                            option.description
                                                        "
                                                    ></el-option>
                                                </el-select>
                                                <small
                                                    class="text-danger"
                                                    v-if="
                                                        row.errors
                                                            .payment_destination_id
                                                    "
                                                    v-text="
                                                        row.errors
                                                            .payment_destination_id[0]
                                                    "
                                                ></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="form-group mb-0"
                                                :class="{
                                                    'has-danger':
                                                        row.errors.reference,
                                                }"
                                            >
                                                <el-input
                                                    v-model="row.reference"
                                                ></el-input>
                                                <small
                                                    class="text-danger"
                                                    v-if="row.errors.reference"
                                                    v-text="
                                                        row.errors.reference[0]
                                                    "
                                                ></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <el-upload
                                                    :data="{ index: index }"
                                                    :headers="headers"
                                                    :multiple="false"
                                                    :on-remove="handleRemove"
                                                    :action="`/finances/payment-file/upload`"
                                                    :show-file-list="true"
                                                    :file-list="fileList"
                                                    :on-success="onSuccess"
                                                    :limit="1"
                                                >
                                                    <el-button
                                                        slot="trigger"
                                                        type="primary"
                                                        ><i
                                                            class="fas fa-file-upload"
                                                        ></i
                                                    ></el-button>
                                                </el-upload>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="form-group mb-0"
                                                :class="{
                                                    'has-danger':
                                                        row.errors.payment,
                                                }"
                                            >
                                                <el-input
                                                    v-model="row.payment"
                                                ></el-input>
                                                <small
                                                    class="text-danger"
                                                    v-if="row.errors.payment"
                                                    v-text="
                                                        row.errors.payment[0]
                                                    "
                                                ></small>
                                            </div>
                                        </td>
                                        <td
                                            class="series-table-actions text-end"
                                        >
                                            <div
                                                class="d-flex justify-content-center"
                                            >
                                                <button
                                                    type="button"
                                                    class="btn waves-effect waves-light btn-sm btn-info"
                                                    @click.prevent="
                                                        clickSubmit(index)
                                                    "
                                                >
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn waves-effect waves-light btn-sm btn-danger"
                                                    @click.prevent="
                                                        clickCancel(index)
                                                    "
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        TOTAL PAGADO
                                    </td>
                                    <td class="text-end">
                                        {{ document.total_paid }}
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        TOTAL A PAGAR
                                    </td>
                                    <td class="text-end">
                                        {{ document.total }}
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        PENDIENTE DE PAGO
                                    </td>
                                    <td class="text-end">
                                        {{ document.total_difference }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div
                    class="col-md-12 text-center pt-2"
                    v-if="showAddButton && document.total_difference > 0"
                >
                    <el-button
                        type="primary"
                        icon="el-icon-plus"
                        @click="clickAddRow"
                        >Nuevo</el-button
                    >
                </div>
            </div>
        </div>

        <document-options
            :recordId="this.documentId"
            :showDialogOptions.sync="showDialogOptions"
            :showClose="showDialogClose"
            :type="this.type"
            :configuration="this.configuration"
        ></document-options>

        <option-documents-prepayment
            @getData="getData"
            :isPrepaymentDeduction="isPrepaymentDeduction"
            :show.sync="showDialogOptionsPrepayment"
            :recordId="saleNoteId"
            :currentPayment.sync="currentPayment"
            :showClose="true"
        ></option-documents-prepayment>
    </el-dialog>
</template>

<style scope>
.el-upload-list__item-name [class^="el-icon"] {
    display: none;
}
.el-upload-list__item-name {
    margin-right: 25px;
}
.el-upload-list__item {
    font-size: 10px;
}
.ml-2 {
    margin-left: 5px;
}
</style>

<script>
import { deletable } from "../../../../mixins/deletable";
import OptionDocumentsPrepayment from "./option_documents_prepayments.vue";
import DocumentOptions from "../../../../../../modules/Finance/Resources/assets/js/views/unpaid/partials/options";

export default {
    props: ["showDialog", "documentId", "external", "configuration"],
    mixins: [deletable],
    components: {
        DocumentOptions,
        OptionDocumentsPrepayment,
    },
    data() {
        return {
            isPrepaymentDeduction: false,
            currentPayment: null,
            showDialogOptionsPrepayment: false,
            title: null,
            resource: "sale_note_payments",
            records: [],
            payment_destinations: [],
            payment_method_types: [],
            headers: headers_token,
            index_file: null,
            fileList: [],
            showAddButton: true,
            document: {},
            showDialogOptions: false,
            showDialogClose: false,
            type: "sale",
            form: {
                currency_type_id: null,
                exchange_rate_sale: 1,
            },
            saleNoteId: null,
        };
    },
    async created() {
        await this.initForm();
        await this.$http.get(`/${this.resource}/tables`).then((response) => {
            this.payment_destinations = response.data.payment_destinations;
            this.payment_method_types = response.data.payment_method_types;
            //this.initDocumentTypes()
        });
    },
    methods: {
        processPayments() {
            const groupedByDate = this.records.reduce(
                (acc, payment) => {
                    const key = `${payment.date_of_payment}-${
                        payment.document_prepayment_id === null
                            ? "null"
                            : "notnull"
                    }`;

                    if (!acc[key]) {
                        acc[key] = [];
                    }
                    acc[key].push(payment);

                    return acc;
                },
                {}
            );

            Object.keys(groupedByDate).forEach((date) => {
                let totalPayment = 0;

                groupedByDate[date].forEach((payment) => {
                    totalPayment += parseFloat(payment.payment);
                });

                groupedByDate[date].forEach((payment, index) => {
                    payment.show = index === 0;
                    if (index === 0) {
                        payment.total_payment = totalPayment;
                    }
                });
            });

            return [].concat(...Object.values(groupedByDate));
        },
        // processPayments() {
        //     const groupedByDate = this.records.reduce((acc, payment) => {
        //         if (!acc[payment.date_of_payment]) {
        //             acc[payment.date_of_payment] = [];
        //         }
        //         acc[payment.date_of_payment].push(payment);
        //         return acc;
        //     }, {});

        //     Object.keys(groupedByDate).forEach((date) => {
        //         let totalPayment = 0;

        //         groupedByDate[date].forEach((payment) => {
        //             totalPayment += parseFloat(payment.payment);
        //         });

        //         groupedByDate[date].forEach((payment, index) => {
        //             payment.show = index === 0;
        //             if (index === 0) {
        //                 payment.total_payment = totalPayment;
        //             }
        //         });
        //     });

        //     return [].concat(...Object.values(groupedByDate));
        // },
        onPrepareItems(items) {
            return items.map((i) => {
                i.unit_price_value = Number(i.unit_value);
                i.input_unit_price_value = i.item.has_igv
                    ? i.item.unit_price
                    : i.unit_value;

                i.discounts = i.discounts ? Object.values(i.discounts) : [];
                let total_discount = 0;
                if (i.discounts.length > 0) {
                    total_discount = i.discounts.reduce((acc, discount) => {
                        return acc + Number(discount.amount);
                    }, 0);
                }
                i.total_discount = total_discount;
                // i.discounts = i.discounts || [];
                i.charges = i.charges || [];
                i.attributes = i.attributes || [];
                i.item.id = i.item_id;
                i.item.original_quantity = i.quantity;
                i.additional_information = this.onPrepareAdditionalInformation(
                    i.additional_information
                );
                i.discounts_acc = i.discounts_acc
                    ? Object.values(i.discounts_acc)
                    : [];
                i.item = this.onPrepareIndividualItem(i);
                return i;
            });
        },
        onPrepareIndividualItem(data) {
            let new_item = data.item;
            let currency_type = _.find(this.currency_types, {
                id: this.form.currency_type_id,
            });
            new_item.stock = data.stock;
            new_item.sale_affectation_igv_type_id =
                data.affectation_igv_type_id;
            let { discounts } = data;
            // let amount = 0;
            let { percentage_igv } = data;
            let amount = discounts.reduce((acc, discount) => {
                if (discount.discount_type_id == "00") {
                    return (
                        acc +
                        Number(discount.amount) * (percentage_igv / 100 + 1)
                    );
                } else {
                    return acc + Number(discount.amount);
                }
            }, 0);
            let { exchange_rate_sale } = this.form;
            if (new_item.currency_type_id != this.form.currency_type_id) {
                new_item.sale_unit_price =
                    (Number(new_item.unit_price) + amount) * exchange_rate_sale;
                new_item.unit_price =
                    (Number(new_item.unit_price) + amount) * exchange_rate_sale;
            } else {
                new_item.sale_unit_price = Number(new_item.unit_price) + amount;
                new_item.unit_price = Number(new_item.unit_price) + amount;
            }
            // new_item.currency_type_id = currency_type.id;
            // new_item.currency_type_symbol = currency_type.symbol;
            return new_item;
        },
        onPrepareAdditionalInformation(data) {
            let obs = null;
            if (Array.isArray(data)) {
                if (data.length > 0) {
                    if (data[0] == "") {
                        return obs;
                    }
                }
                return data.join("|");
            }

            return data;
        },
        clickDocumentPrePayment(idx, isPrepaymentDeduction = false) {
            this.isPrepaymentDeduction = isPrepaymentDeduction;
            let record = this.records[idx];
            this.saleNoteId = record.sale_note_id;
            this.showDialogOptionsPrepayment = true;
            this.currentPayment = record;
        },
        clickDownloadFile(filename) {
            window.open(
                `/finances/payment-file/download-file/${filename}/sale_notes`,
                "_blank"
            );
        },
        onSuccess(response, file, fileList) {
            // console.log(response, file, fileList)
            this.fileList = fileList;

            if (response.success) {
                this.index_file = response.data.index;
                this.records[this.index_file].filename = response.data.filename;
                this.records[this.index_file].temp_path =
                    response.data.temp_path;
            } else {
                this.cleanFileList();
                this.$message.error(response.message);
            }

            // console.log(this.records)
        },
        cleanFileList() {
            this.fileList = [];
        },
        handleRemove(file, fileList) {
            this.records[this.index_file].filename = null;
            this.records[this.index_file].temp_path = null;
            this.fileList = [];
            this.index_file = null;
        },
        initForm() {
            this.records = [];
            this.fileList = [];
            this.showAddButton = true;
        },
        async getData() {
            this.initForm();
            await this.$http
                .get(`/${this.resource}/document/${this.documentId}`)
                .then((response) => {
                    this.document = response.data;
                    this.title =
                        "Pagos del comprobante: " + this.document.number_full;
                });
            await this.$http
                .get(`/${this.resource}/records/${this.documentId}`)
                .then((response) => {
                    this.records = response.data.data;
                    this.records = this.processPayments();
                });
            this.$eventHub.$emit("reloadDataUnpaid");
        },
        clickAddRow() {
            this.records.push({
                id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: null,
                payment_destination_id: null,
                reference: null,
                filename: null,
                temp_path: null,
                payment: 0,
                errors: {},
                loading: false,
            });
            this.showAddButton = false;
        },
        clickCancel(index) {
            this.records.splice(index, 1);
            this.showAddButton = true;
            this.fileList = [];
        },
        clickSubmit(index) {


            if (
                this.records[index].payment >
                parseFloat(this.document.total_difference)
            ) {
                this.$message.error(
                    "El monto ingresado supera al monto pendiente de pago, verifique."
                );
                return;
            }
            let paid = false;
            if (
                parseFloat(this.records[index].payment) ==
                parseFloat(this.document.total_difference)
            ) {
                paid = true;
            }

            let form = {
                id: this.records[index].id,
                sale_note_id: this.documentId,
                date_of_payment: this.records[index].date_of_payment,
                payment_method_type_id:
                    this.records[index].payment_method_type_id,
                payment_destination_id:
                    this.records[index].payment_destination_id,
                reference: this.records[index].reference,
                filename: this.records[index].filename,
                temp_path: this.records[index].temp_path,
                payment: this.records[index].payment,
                paid: paid,
            };
            this.$http
                .post(`/${this.resource}`, form)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.getData();
                        // this.initDocumentTypes()
                        this.$eventHub.$emit("reloadData");
                        this.showAddButton = true;
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.records[index].errors = error.response.data;
                    } else {
                        console.log(error);
                        this.$message.error(error.response.data.message);
                    }
                });
        },
        // filterDocumentType(row){
        //
        //     if(row.contingency){
        //         this.document_types = _.filter(this.all_document_types, item => (item.id == '01' || item.id =='03'))
        //         row.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
        //     }else{
        //         row.document_type_id = null
        //         this.document_types = this.all_document_types
        //     }
        // },
        // initDocumentTypes(){
        //     this.document_types = (this.all_document_types.length > 0) ? this.all_document_types : []
        // },
        close() {
            this.$emit("update:showDialog", false);
            // this.initDocumentTypes()
            // this.initForm()
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(
                () => {
                    this.getData();
                    this.$eventHub.$emit("reloadData");
                }
                // this.initDocumentTypes()
            );
        },
        clickPrint(external_id) {
            window.open(`/finances/unpaid/print/${external_id}/sale`, "_blank");
        },
        clickOptions() {
            this.showDialogOptions = true;
            this.showDialogClose = true;
        },
    },
};
</script>
