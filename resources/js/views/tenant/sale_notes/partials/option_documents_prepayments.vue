<template>
    <div>
        <el-dialog
            :title="titleDialog"
            :visible="show"
            @open="create"
            width="50%"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :show-close="false"
        >
            <div class="row">
                <div class="col-lg-8">
                    <div
                        class="form-group"
                        :class="{ 'has-danger': errors.document_type_id }"
                    >
                        <label class="control-label">Tipo comprobante</label>
                        <el-select
                            disabled
                            v-model="document.document_type_id"
                            @change="changeDocumentType"
                            popper-class="el-select-document_type"
                            dusk="document_type_id"
                        >
                            <el-option
                                v-for="option in document_types"
                                :key="option.id"
                                :value="option.id"
                                :label="option.description"
                            ></el-option>
                        </el-select>
                        <small
                            class="text-danger"
                            v-if="errors.document_type_id"
                            v-text="errors.document_type_id[0]"
                        ></small>
                        <!-- <el-checkbox  v-model="generate_dispatch">Generar Guía Remisión</el-checkbox> -->
                    </div>
                </div>
                <div class="col-lg-4">
                    <div
                        class="form-group"
                        :class="{ 'has-danger': errors.series_id }"
                    >
                        <label class="control-label">Serie</label>
                        <el-select v-model="document.series_id">
                            <el-option
                                v-for="option in series"
                                :key="option.id"
                                :value="option.id"
                                :label="option.number"
                            ></el-option>
                        </el-select>
                        <small
                            class="text-danger"
                            v-if="errors.series_id"
                            v-text="errors.series_id[0]"
                        ></small>
                    </div>
                </div>

                <!--    <div class="col-xl-3 col-md-3 col-12 pt-2 pb-2">
                        <span class="mr-2">Deducción de anticipados</span>
                        <el-switch
                            v-model="prepayment_deduction"
                            @change="changePrepaymentDeduction"
                        ></el-switch>
                    </div>-->
                <div class="col-lg-12">
                    <div class="form-group">
                        <label class="control-label">Observaciones</label>
                        <el-input
                            type="textarea"
                            autosize
                            v-model="document.additional_information"
                        >
                        </el-input>
                    </div>
                </div>
                <!-- <div class="col-lg-4 col-md-4">
                    <div
                        :class="{ 'has-danger': errors.seller_id }"
                        class="form-group"
                    >
                        <label class="control-label">Vendedor</label>
                        <el-select v-model="document.seller_id" clearable>
                            <el-option
                                v-for="option in sellers"
                                :key="option.id"
                                :label="option.name"
                                :value="option.id"
                                >{{ option.name }}
                            </el-option>
                        </el-select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div
                        class="form-group"
                        :class="{ 'has-danger': errors.payment_condition_id }"
                    >
                        <label class="control-label">Condición de pago</label>
                        <el-select
                            v-model="document.payment_condition_id"
                            @change="changePaymentCondition"
                            popper-class="el-select-document_type"
                            dusk="document_type_id"
                            style="max-width: 200px"
                        >
                            <el-option value="02" label="Crédito"></el-option>
                            <el-option value="01" label="Contado"></el-option>
                        </el-select>
                        <small
                            class="text-danger"
                            v-if="errors.date_of_due"
                            v-text="errors.date_of_due[0]"
                        ></small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div
                        class="form-group"
                        :class="{ 'has-danger': errors.date_of_issue }"
                    >
                        <label class="control-label">Fecha de emisión</label>
                        <el-date-picker
                            readonly
                            v-model="document.date_of_issue"
                            type="date"
                            value-format="yyyy-MM-dd"
                            :clearable="false"
                            @change="changeDateOfIssue"
                        ></el-date-picker>
                        <small
                            class="text-danger"
                            v-if="errors.date_of_issue"
                            v-text="errors.date_of_issue[0]"
                        ></small>
                    </div>
                </div>
 -->
                <!-- <div class="col-lg-4">
                    <div
                        class="form-group"
                        :class="{ 'has-danger': errors.date_of_issue }"
                    >
                        <label class="control-label"
                            >Fecha de vencimiento</label
                        >
                        <el-date-picker
                            v-model="document.date_of_due"
                            type="date"
                            value-format="yyyy-MM-dd"
                            :clearable="false"
                        ></el-date-picker>
                        <small
                            class="text-danger"
                            v-if="errors.date_of_due"
                            v-text="errors.date_of_due[0]"
                        ></small>
                    </div>
                </div>
                <br />
                <div class="col-lg-8 mt-3">
                    <div
                        class="form-group"
                        :class="{ 'has-danger': errors.dipatch_id }"
                    >
                        <el-checkbox v-model="generate_dispatch"
                            >Generar Guía Remisión</el-checkbox
                        >

                        <el-select
                            v-model="form.dispatch_id"
                            popper-class="el-select-document_type"
                            filterable
                            remote
                            clearable
                            placeholder="Buscar producto"
                            :remote-method="searchDispatches"
                            :loading="loading_search"
                            v-if="generate_dispatch"
                        >
                            <el-option
                                v-for="option in dispatches"
                                :key="option.id"
                                :value="option.id"
                                :label="option.number_full"
                            ></el-option>
                        </el-select>
                        <small
                            class="text-danger"
                            v-if="errors.dipatch_id"
                            v-text="errors.dipatch_id[0]"
                        ></small>
                    </div>
                </div>

                <div
                    class="col-lg-12 pt-2"
                    v-show="document.payment_condition_id === '02'"
                >
                    <table v-if="document.fee.length > 0" width="100%">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th style="width: 30px">
                                    <a
                                        v-if="!blockAddPayments"
                                        style="font-size: 18px"
                                        href="#"
                                        @click.prevent="clickAddFee"
                                        class="text-center font-weight-bold text-center text-info"
                                        >[+]</a
                                    >
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, index) in document.fee"
                                :key="index"
                            >
                                <td v-if="document.fee.length > 0">
                                    <div class="form-group mb-2 mr-2">
                                        <el-date-picker
                                            v-model="row.date"
                                            type="date"
                                            value-format="yyyy-MM-dd"
                                            format="dd/MM/yyyy"
                                            @change="
                                                changeDatePaymentCondition(
                                                    index
                                                )
                                            "
                                            :clearable="false"
                                        ></el-date-picker>
                                    </div>
                                </td>
                                <td v-if="document.fee.length > 0">
                                    <div class="form-group mb-2 mr-2">
                                        <el-input
                                            v-model="row.amount"
                                        ></el-input>
                                    </div>
                                </td>
                                <td class="series-table-actions text-center">
                                    <button
                                        type="button"
                                        class="btn waves-effect waves-light btn-sm btn-danger"
                                        @click.prevent="clickRemoveFee(index)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>  -->
                <!-- <div
                    class="col-lg-12"
                    v-show="document.payment_condition_id != '02' && !payed"
                >
                    <table>
                        <thead>
                            <tr width="100%">
                                <th v-if="document.payments.length > 0">
                                    M.Pago
                                </th>
                                <th v-if="document.payments.length > 0">
                                    Destino
                                </th>
                                <th v-if="document.payments.length > 0">
                                    Referencia
                                </th>
                                <th v-if="document.payments.length > 0">
                                    Monto
                                </th>
                                <th width="5%">
                                    <a
                                        style="font-size: 18px"
                                        href="#"
                                        @click.prevent="clickAddPayment"
                                        class="text-center font-weight-bold text-center text-info"
                                        >[+]</a
                                    >
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, index) in document.payments"
                                :key="index"
                            >
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-select
                                            v-model="row.payment_method_type_id"
                                        >
                                            <el-option
                                                v-for="option in payment_method_types"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.description"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-select
                                            v-model="row.payment_destination_id"
                                            filterable
                                            :disabled="
                                                row.payment_destination_disabled
                                            "
                                            @change="
                                                changePaymentDestination(index)
                                            "
                                        >
                                            <el-option
                                                v-for="option in payment_destinations"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.description"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-input
                                            v-model="row.reference"
                                        ></el-input>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-2 mr-2">
                                        <el-input
                                            v-model="row.payment"
                                        ></el-input>
                                    </div>
                                </td>
                                <td class="series-table-actions text-center">
                                    <button
                                        type="button"
                                        class="btn waves-effect waves-light btn-sm btn-danger"
                                        @click.prevent="clickCancel(index)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <br />
                            </tr>
                        </tbody>
                    </table>
                </div> -->

                <!-- <template v-if="fnApplyRestrictSaleItemsCpe">
                    <list-restrict-items
                        v-if="load_list_document_items"
                        :form="document"
                        :configuration="configuration"
                        :globalDiscountTypes="global_discount_types"
                        class="mt-3"
                    >
                    </list-restrict-items>
                </template> -->
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="clickClose">Cerrar</el-button>
                <el-button
                    class="submit"
                    type="primary"
                    @click="submit"
                    :loading="loading_submit"
                    v-if="flag_generate"
                    >Generar</el-button
                >
            </span>

            <document-options
                :showDialog.sync="showDialogDocumentOptions"
                :recordId="documentNewId"
                :generatDispatch="generate_dispatch"
                :dispatchId="dispatch_id"
                :isContingency="false"
                :showClose="true"
            ></document-options>
            <div class="col-lg-12" v-show="document.total > 0">
                <div class="form-group pull-right">
                    <label class="control-label">
                        Total
                        {{ document.currency_type_id == "PEN" ? "S/" : "$." }}
                        {{ document.total }}</label
                    >
                </div>
                <br />
            </div>
        </el-dialog>
    </div>
</template>

<script>
import DocumentOptions from "../../documents/partials/options.vue";
import moment from "moment";
import ListRestrictItems from "@components/secondary/ListRestrictItems.vue";
import { fnRestrictSaleItemsCpe, advance } from "@mixins/functions";
import { calculateRowItem } from "@helpers/functions";
export default {
    components: { DocumentOptions, ListRestrictItems },
    mixins: [fnRestrictSaleItemsCpe, advance],
    props: [
        "show",
        "recordId",
        "showClose",
        "showGenerate",
        "currentPayment",
        "isPrepaymentDeduction",
    ],
    data() {
        return {
            percentage_igv: 18,
            form_cash_document: {},
            timer: null,
            loading_search: false,
            payed: true,
            sellers: [],
            titleDialog: null,
            loading: false,
            resource: "sale-notes",
            resource_documents: "documents",
            errors: {},
            form: {},
            document: {},
            document_types: [],
            all_document_types: [],
            all_series: [],
            series: [],
            generate: false,
            loading_submit: false,
            showDialogDocumentOptions: false,
            documentNewId: null,
            flag_generate: true,
            dispatches: [],
            generate_dispatch: false,
            dispatch_id: null,
            payment_destinations: [],
            payment_method_types: [],
            payment_condition_id: "01",
            fee: [],
            configuration: {},
            global_discount_types: [],
            load_list_document_items: false,
            cash_id: null,
            prepayment_documents: [],
            prepayment_deduction: false,
        };
    },
    created() {
        this.initDocument();

        // console.log(moment().format('YYYY-MM-DD'))
    },
    computed: {
        blockAddPayments() {
            return (
                this.form.payments.filter(
                    (payment) => payment.payment_destination_id === "advance"
                ).length > 0
            );
        },
    },
    methods: {
        getAffectationTypePrepayment() {
            return this.document.items[0].affectation_igv_type_id;
        },
        getDocumentsPrepayment() {
            this.$http
                .get(
                    `/sale_note_payments/get_document_prepayments/${this.recordId}`
                )
                .then((response) => {
                    let data = response.data;
                    this.prepayment_documents = data.documents;
                    this.document.prepayments = data.documents;
                    this.calculateTotal();
                });
        },
        discountGlobalPrepayment() {
            let global_discount = 0;
            let sum_total_prepayment = 0;

            this.document.prepayments.forEach((item) => {
                global_discount += parseFloat(item.amount);
                sum_total_prepayment += parseFloat(item.total);
            });

            // let base = (this.form.affectation_type_prepayment == 10) ? parseFloat(this.form.total_taxed):parseFloat(this.form.total_exonerated)
            let base = 0;
        
                console.log("🚀 ~ discountGlobalPrepayment ~ this.document.affectation_type_prepayment:", this.document.affectation_type_prepayment)
            switch (Number(this.document.affectation_type_prepayment)) {
                case 10:
                    base =
                        parseFloat(this.document.total_taxed) + global_discount;
                    // base = parseFloat(this.form.total_taxed)
                    console.log("🚀 ~ discountGlobalPrepayment ~ global_discount:", global_discount)
                    console.log("🚀 ~ discountGlobalPrepayment ~ this.document.total_taxed:", this.document.total_taxed)
                    break;
                case 20:
                    base =
                        parseFloat(this.document.total_exonerated) +
                        global_discount;
                    break;
                case 30:
                    base =
                        parseFloat(this.document.total_unaffected) +
                        global_discount;
                    break;
            }

            console.log("🚀 ~ discountGlobalPrepayment ~ base:", base);
            let amount = _.round(global_discount, 2);
            console.log("🚀 ~ discountGlobalPrepayment ~ amount:", amount);
            let factor = _.round(amount / base, 5);
            console.log("🚀 ~ discountGlobalPrepayment ~ factor:", factor);

            this.document.total_prepayment = _.round(sum_total_prepayment, 2);
            // this.form.total_prepayment = _.round(global_discount, 2)

            if (this.document.affectation_type_prepayment == 10) {
                let discount = _.find(this.document.discounts, {
                    discount_type_id: "04",
                });

                if (global_discount > 0 && !discount) {
                    this.document.total_discount = _.round(amount, 2);
                    this.document.total_taxed = _.round(
                        this.document.total_taxed - amount,
                        2
                    );
                    this.document.total_igv = _.round(
                        this.document.total_taxed * (this.percentage_igv / 100),
                        2
                    );
                    this.document.total_taxes = _.round(
                        this.document.total_igv,
                        2
                    );
                    this.document.total = _.round(
                        this.document.total_taxed + this.document.total_taxes,
                        2
                    );

                    this.document.discounts.push({
                        discount_type_id: "04",
                        description:
                            "Descuentos globales por anticipos gravados que afectan la base imponible del IGV/IVAP",
                        factor: factor,
                        amount: amount,
                        base: base,
                    });
                } else {
                    let pos = this.document.discounts.indexOf(discount);

                    if (pos > -1) {
                        this.document.total_discount = _.round(amount, 2);
                        this.document.total_taxed = _.round(
                            this.document.total_taxed - amount,
                            2
                        );
                        this.document.total_igv = _.round(
                            this.document.total_taxed *
                                (this.percentage_igv / 100),
                            2
                        );
                        this.document.total_taxes = _.round(
                            this.document.total_igv,
                            2
                        );
                        this.document.total = _.round(
                            this.document.total_taxed +
                                this.document.total_taxes,
                            2
                        );

                        this.document.discounts[pos].base = base;
                        this.document.discounts[pos].amount = amount;
                        this.document.discounts[pos].factor = factor;
                    }
                }
            } else if (this.document.affectation_type_prepayment == 20) {
                let exonerated_discount = _.find(this.document.discounts, {
                    discount_type_id: "05",
                });

                this.document.total_discount = _.round(amount, 2);
                this.document.total_exonerated = _.round(
                    this.document.total_exonerated - amount,
                    2
                );
                this.document.total = this.document.total_exonerated;

                if (global_discount > 0 && !exonerated_discount) {
                    this.document.discounts.push({
                        discount_type_id: "05",
                        description:
                            "Descuentos globales por anticipos exonerados",
                        factor: factor,
                        amount: amount,
                        base: base,
                    });
                } else {
                    let position =
                        this.document.discounts.indexOf(exonerated_discount);

                    if (position > -1) {
                        this.document.discounts[position].base = base;
                        this.document.discounts[position].amount = amount;
                        this.document.discounts[position].factor = factor;
                    }
                }
            } else if (this.document.affectation_type_prepayment == 30) {
                let unaffected_discount = _.find(this.document.discounts, {
                    discount_type_id: "06",
                });

                this.document.total_discount = _.round(amount, 2);
                this.document.total_unaffected = _.round(
                    this.document.total_unaffected - amount,
                    2
                );
                this.document.total = this.document.total_unaffected;

                if (global_discount > 0 && !unaffected_discount) {
                    this.document.discounts.push({
                        discount_type_id: "06",
                        description:
                            "Descuentos globales por anticipos inafectos",
                        factor: factor,
                        amount: amount,
                        base: base,
                    });
                } else {
                    let position =
                        this.document.discounts.indexOf(unaffected_discount);
                    if (position > -1) {
                        this.document.discounts[position].base = base;
                        this.document.discounts[position].amount = amount;
                        this.document.discounts[position].factor = factor;
                    }
                }
            }
        },
        async validateAffectationTypePrepayment() {
            let not_equal_affectation_type = 0;

            console.log(
                "🚀 ~ awaitthis.document.items.forEach ~ this.document.items:",
                this.document
            );
            await this.document.items.forEach((item) => {
                if (
                    item.affectation_igv_type_id !=
                    this.document.affectation_type_prepayment
                ) {
                    not_equal_affectation_type++;
                }
            });

            return {
                success: not_equal_affectation_type > 0 ? false : true,
                message:
                    "Los items deben tener tipo de afectación igual al seleccionado en el anticipo",
            };
        },
        async changeDocumentPrepayment(index) {
            let prepayment = await _.find(this.prepayment_documents, {
                id: this.document.prepayments[index].document_id,
            });

            this.document.prepayments[index].number = prepayment.description;
            this.document.prepayments[index].document_type_id =
                prepayment.document_type_id;
            this.document.prepayments[index].amount = prepayment.amount;
            this.document.prepayments[index].total = prepayment.total;

            await this.changeTotalPrepayment();
        },
        async deletePrepaymentDiscount() {
            let discount = await _.find(this.document.discounts, {
                discount_type_id: "04",
            });
            let discount_exonerated = await _.find(this.document.discounts, {
                discount_type_id: "05",
            });
            let discount_unaffected = await _.find(this.document.discounts, {
                discount_type_id: "06",
            });

            let pos = this.document.discounts.indexOf(discount);
            if (pos > -1) {
                this.document.discounts.splice(pos, 1);
                this.changeTotalPrepayment();
            }

            let pos_exonerated =
                this.document.discounts.indexOf(discount_exonerated);
            if (pos_exonerated > -1) {
                this.document.discounts.splice(pos_exonerated, 1);
                this.changeTotalPrepayment();
            }

            let pos_unaffected =
                this.document.discounts.indexOf(discount_unaffected);
            if (pos_unaffected > -1) {
                this.document.discounts.splice(pos_unaffected, 1);
                this.changeTotalPrepayment();
            }
        },
        async changePrepaymentDeduction() {
            this.document.prepayments = [];
            this.document.total_prepayment = 0;
            await this.deletePrepaymentDiscount();

            if (this.prepayment_deduction) {
                await this.initialValueATPrepayment();
                await this.changeTotalPrepayment();
                await this.getDocumentsPrepayment();
            } else {
                // this.form.total_prepayment = 0
                // await this.deletePrepaymentDiscount()
                this.cleanValueATPrepayment();
            }
        },
        calculatePercentage() {
            let { total: totalGeneral } = this.document;
            this.document.items = this.document.items.map((item) => {
                let { total } = item;
                let percentage = (total / totalGeneral) * 100;
                item.percentageTotal = percentage;
                return item;
            });
        },

        calculateNewPartialTotal(newTotal) {
            this.document.items = this.document.items.map((item) => {
                let percentageTotal = item.percentageTotal;
                let newPartialTotal = (percentageTotal / 100) * newTotal;
                item.newPartialTotal = newPartialTotal;
                return item;
            });
        },

        splitByQuantity() {
            this.document.items = this.document.items.map((item) => {
                let newPartialTotal = item.newPartialTotal;
                let quantity = item.quantity;
                let newUnitPrice = newPartialTotal / quantity;
                item.newUnitPrice = newUnitPrice;
                return item;
            });
        },
        changeUnitPriceItem(item, index) {
            let new_value = item.newUnitPrice;
            let {
                percentage_igv,
                item: { has_igv },
            } = item;
            let unit_value = 0;
            if (has_igv) {
                unit_value = new_value / (1 + percentage_igv / 100);
            } else {
                unit_value = new_value;
            }
            let unit_price = new_value;
            item.item.unit_price = unit_price;
            this.document.items[index] = calculateRowItem(
                item,
                this.document.currency_type_id,
                this.document.exchange_rate_sale,
                this.percentage_igv / 100
            );
            this.document.items[index].unit_value_edit = _.round(unit_value, 4);
            this.document.items[index].unit_price_edit = _.round(unit_price, 4);
            this.$forceUpdate();
            this.calculateTotal();
        },
        changeTotalPrepayment() {
            this.calculateTotal();
        },
        calculateTotal() {
            let total_discount = 0;
            let total_charge = 0;
            let total_exportation = 0;
            let total_taxed = 0;
            let total_exonerated = 0;
            let total_unaffected = 0;
            let total_free = 0;
            let total_igv = 0;
            let total_value = 0;
            let total = 0;
            let total_plastic_bag_taxes = 0;
            this.total_discount_no_base = 0;

            let total_igv_free = 0;
            let total_base_isc = 0;
            let total_isc = 0;

            // let total_free_igv = 0

            this.document.items.forEach((row) => {
                total_discount += parseFloat(row.total_discount);
                total_charge += parseFloat(row.total_charge);

                if (row.affectation_igv_type_id === "10") {
                    // total_taxed += parseFloat(row.total_value)
                    if (row.total_value_without_rounding) {
                        total_taxed += parseFloat(
                            row.total_value_without_rounding
                        );
                    } else {
                        total_taxed += parseFloat(row.total_value);
                    }
                }

                if (
                    row.affectation_igv_type_id === "20" // 20,Exonerado - Operación Onerosa
                    // || row.affectation_igv_type_id === '21' // 21,Exonerado – Transferencia Gratuita
                ) {
                    // total_exonerated += parseFloat(row.total_value)

                    total_exonerated += row.total_value_without_rounding
                        ? parseFloat(row.total_value_without_rounding)
                        : parseFloat(row.total_value);
                }

                if (
                    row.affectation_igv_type_id === "30" || // 30,Inafecto - Operación Onerosa
                    row.affectation_igv_type_id === "31" || // 31,Inafecto – Retiro por Bonificación
                    row.affectation_igv_type_id === "32" || // 32,Inafecto – Retiro
                    row.affectation_igv_type_id === "33" || // 33,Inafecto – Retiro por Muestras Médicas
                    row.affectation_igv_type_id === "34" || // 34,Inafecto - Retiro por Convenio Colectivo
                    row.affectation_igv_type_id === "35" || // 35,Inafecto – Retiro por premio
                    row.affectation_igv_type_id === "36" // 36,Inafecto - Retiro por publicidad
                    // || row.affectation_igv_type_id === '37'  // 37,Inafecto - Transferencia gratuita
                ) {
                    total_unaffected += parseFloat(row.total_value);
                }

                if (row.affectation_igv_type_id === "40") {
                    total_exportation += parseFloat(row.total_value);
                }

                if (
                    [
                        "10",
                        // '20', '21',
                        "20",
                        "30",
                        "31",
                        "32",
                        "33",
                        "34",
                        "35",
                        "36",
                        "40",
                    ].indexOf(row.affectation_igv_type_id) < 0
                ) {
                    total_free += parseFloat(row.total_value);
                }

                if (
                    [
                        "10",
                        "20",
                        "21",
                        "30",
                        "31",
                        "32",
                        "33",
                        "34",
                        "35",
                        "36",
                        "40",
                    ].indexOf(row.affectation_igv_type_id) > -1
                ) {
                    // total_igv += parseFloat(row.total_igv)
                    // total += parseFloat(row.total)
                    if (row.total_igv_without_rounding) {
                        total_igv += parseFloat(row.total_igv_without_rounding);
                    } else {
                        total_igv += parseFloat(row.total_igv);
                        console.log(
                            "🚀 ~ this.document.items.forEach ~ total_igv:",
                            total_igv
                        );
                    }

                    // row.total_value_without_rounding = total_value
                    // row.total_base_igv_without_rounding = total_base_igv
                    // row.total_igv_without_rounding = total_igv
                    // row.total_taxes_without_rounding = total_taxes
                    // row.total_without_rounding = total

                    if (row.total_without_rounding) {
                        total += parseFloat(row.total_without_rounding);
                    } else {
                        total += parseFloat(row.total);
                    }
                }

                if (!["21", "37"].includes(row.affectation_igv_type_id)) {
                    // total_value += parseFloat(row.total_value)
                    if (row.total_value_without_rounding) {
                        total_value += parseFloat(
                            row.total_value_without_rounding
                        );
                    } else {
                        total_value += parseFloat(row.total_value);
                    }
                }

                total_plastic_bag_taxes += parseFloat(
                    row.total_plastic_bag_taxes
                );

                if (
                    ["11", "12", "13", "14", "15", "16"].includes(
                        row.affectation_igv_type_id
                    )
                ) {
                    let unit_value = row.total_value / row.quantity;
                    let total_value_partial = unit_value * row.quantity;
                    // row.total_taxes = row.total_value - total_value_partial
                    row.total_taxes =
                        row.total_value -
                        total_value_partial +
                        parseFloat(row.total_plastic_bag_taxes); //sumar icbper al total tributos

                    row.total_igv =
                        total_value_partial * (row.percentage_igv / 100);

                    row.total_base_igv = total_value_partial;
                    total_value -= row.total_value;

                    total_igv_free += row.total_igv;
                    total += parseFloat(row.total); //se agrega suma al total para considerar el icbper
                }

                //sum discount no base
                this.total_discount_no_base += 0;
                // isc
                total_isc += parseFloat(row.total_isc);
                total_base_isc += parseFloat(row.total_base_isc);
            });

            // isc
            this.document.total_base_isc = _.round(total_base_isc, 2);
            this.document.total_isc = _.round(total_isc, 2);

            this.document.total_igv_free = _.round(total_igv_free, 2);
            this.document.total_discount = _.round(total_discount, 2);
            this.document.total_exportation = _.round(total_exportation, 2);
            this.document.total_taxed = _.round(total_taxed, 2);
            this.document.total_exonerated = _.round(total_exonerated, 2);
            this.document.total_unaffected = _.round(total_unaffected, 2);
            this.document.total_free = _.round(total_free, 2);
            // this.form.total_igv = _.round(total_igv + total_free_igv, 2)
            this.document.total_igv = _.round(total_igv, 2);
            this.document.total_value = _.round(total_value, 2);
            // this.form.total_taxes = _.round(total_igv, 2)

            //impuestos (isc + igv + icbper)
            this.document.total_taxes = _.round(
                total_igv + total_isc + total_plastic_bag_taxes,
                2
            );

            this.document.total_plastic_bag_taxes = _.round(
                total_plastic_bag_taxes,
                2
            );

            this.document.subtotal = _.round(total, 2);
            this.document.total = _.round(
                total - this.total_discount_no_base,
                2
            );
            if (this.prepayment_deduction) this.discountGlobalPrepayment();
        },
        // getDocumentsPrepayment() {
        //     this.$http
        //         .get(
        //             `/documents/prepayments/${this.form.affectation_type_prepayment}`
        //         )
        //         .then((response) => {
        //             this.prepayment_documents = response.data;
        //         });
        // },
        // async changePrepaymentDeduction() {
        //     this.document.prepayments = [];
        //     this.document.total_prepayment = 0;
        //     // await this.deletePrepaymentDiscount();

        //     if (this.prepayment_deduction) {
        //         await this.initialValueATPrepayment();
        //         await this.changeTotalPrepayment();
        //         await this.getDocumentsPrepayment();
        //     } else {
        //         // this.form.total_prepayment = 0
        //         // await this.deletePrepaymentDiscount()
        //         this.cleanValueATPrepayment();
        //     }
        // },
        initialValueATPrepayment() {
            this.document.affectation_type_prepayment =
                this.getAffectationTypePrepayment();
        },
        changeHasPrepayment() {
            if (this.document.has_prepayment) {
                this.initialValueATPrepayment();
            } else {
                this.cleanValueATPrepayment();
            }

            this.setPendingAmount();
        },
        setPendingAmount() {
            this.document.pending_amount_prepayment = this.document
                .has_prepayment
                ? this.document.total
                : 0;
        },
        cleanValueATPrepayment() {
            this.form.affectation_type_prepayment = null;
        },
        changePaymentDestination(index) {
            this.checkHasAdvance(index);
        },
        hasCashOpen() {
            let has_cash = this.payment_destinations.some(
                (payment_destination) => payment_destination.cash_id != null
            );
            if (has_cash) {
                this.cash_id = this.payment_destinations.find(
                    (payment_destination) => payment_destination.cash_id != null
                ).cash_id;
            }
            return has_cash;
        },
        changeDatePaymentCondition(index) {
            const max_date = _.maxBy(this.document.fee, "date");

            if (max_date) this.document.date_of_due = max_date.date;
        },
        changePaymentCondition() {
            this.document.fee = [];
            this.document.payments = [];
            if (this.document.payment_condition_id === "01") {
                this.document.payments = this.form.sale_note.payments;
                if (
                    this.document.payments === undefined ||
                    this.document.payments.length < 1
                ) {
                    this.clickAddPayment();
                }
            }
            if (this.document.payment_condition_id === "02") {
                this.document.fee = this.form.sale_note.fee;
                if (
                    this.document.fee === undefined ||
                    this.document.fee.length < 1
                ) {
                    this.clickAddFee();
                }
            }
        },
        changeDateOfIssue() {
            this.document.date_of_due = this.document.date_of_issue;
        },
        clickRemoveFee(index) {
            this.document.fee.splice(index, 1);
            this.calculateFee();
        },
        clickAddFee() {
            if (this.document.fee === undefined) this.document.fee = [];
            this.document.fee.push({
                id: null,
                date: moment().format("YYYY-MM-DD"),
                currency_type_id: this.document.currency_type_id,
                amount: 0,
            });
            this.calculateFee();
        },
        calculateFee() {
            let fee_count = this.document.fee.length;
            let total = this.document.total;
            let accumulated = 0;
            let amount = _.round(total / fee_count, 2);
            _.forEach(this.document.fee, (row) => {
                accumulated += amount;
                if (total - accumulated < 0) {
                    amount = _.round(total - accumulated + amount, 2);
                }
                row.amount = amount;
            });
        },
        clickCancel(index) {
            this.document.payments.splice(index, 1);
        },
        clickAddPayment() {
            this.document.payments.push({
                id: null,
                document_id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: "01",
                payment_destination_id: null,
                reference: null,
                payment: 0,
            });
        },
        initForm() {
            this.generate = this.showGenerate ? true : false;
            this.errors = {};
            this.form = {
                dispatch_id: null,
                id: null,
                external_id: null,
                identifier: null,
                number_full: null,
                date_of_issue: null,
                seller_id: null,
                sale_note: null,
            };
            this.generate_dispatch = false;
        },
        initDocument() {
            this.document = {
                affectation_type_prepayment: null,
                has_prepayment: true,
                dispatch_id: null,
                document_type_id: null,
                series_id: null,
                establishment_id: null,
                number: "#",
                date_of_issue: null,
                time_of_issue: null,
                customer_id: null,
                currency_type_id: null,
                purchase_order: null,
                exchange_rate_sale: 0,
                total_prepayment: 0,
                total_charge: 0,
                total_discount: 0,
                total_exportation: 0,
                total_free: 0,
                total_taxed: 0,
                total_unaffected: 0,
                total_exonerated: 0,
                total_igv: 0,
                total_base_isc: 0,
                total_isc: 0,
                total_base_other_taxes: 0,
                total_other_taxes: 0,
                total_taxes: 0,
                total_value: 0,
                subtotal: 0,
                total_igv_free: 0,
                total: 0,
                operation_type_id: null,
                date_of_due: null,
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                additional_information: null,
                actions: {
                    format_pdf: "a4",
                },
                quotation_id: null,
                sale_note_id: null,
                payments: [],
                seller_id: null,
                fee: [],
                hotel: {},
            };
        },
        resetDocument() {
            this.generate = this.showGenerate ? true : false;
            this.flag_generate = true;
            this.initDocument();
            this.document.document_type_id =
                this.document_types.length > 0
                    ? this.document_types[0].id
                    : null;
            this.changeDocumentType();

            this.load_list_document_items = false;
        },
        validatePaymentDestination() {
            let error_by_item = 0;
            let message = "";

            this.document.payments.forEach((item) => {
                if (item.payment_destination_id == null) {
                    error_by_item++;
                    message = "El destino del pago es obligatorio";
                }
            });

            return {
                error_by_item: error_by_item,
                message: message,
            };
        },
        validatePaymentDate() {
            let error_by_item = 0;
            let message = "";
            this.document.fee.forEach((item) => {
                // console.error(item)
                var date_issue = moment(this.document.date_of_issue).format(
                    "YYYY-MM-DD"
                );
                var date_payment = moment(item.date).format("YYYY-MM-DD");
                if (date_issue >= date_payment) {
                    error_by_item++;
                    message =
                        "Verificar fechas de pago, no pueden ser anteriores o igual a la fecha de emisión";
                }
            });

            return {
                error_by_item: error_by_item,
                message: message,
            };
        },
        async submit() {
            if (this.document.has_prepayment || this.prepayment_deduction) {
                let error_prepayment =
                    await this.validateAffectationTypePrepayment();
                if (!error_prepayment.success)
                    return this.$message.error(error_prepayment.message);
            }
            let payWithAdvance = this.payWithAdvanceDocument();
            if (payWithAdvance) {
                let enoughAdvance = this.enoughAdvance("document");
                if (!enoughAdvance) {
                    return this.$message.error(
                        "El monto del anticipo no es suficiente para realizar la venta"
                    );
                }
            }
            if (this.configuration.multi_companies) {
                let serie = _.find(this.series, {
                    id: this.document.series_id,
                });
                this.document.series = serie.number;
            } else {
                delete this.document.establishment;
                delete this.document.series;
            }
            if (!this.hasCashOpen()) {
                this.$message.error("Debe abrir caja para realizar la venta");
                return false;
            }
            // validacion restriccion de productos
            const validate_restrict_sale_items_cpe =
                this.fnValidateRestrictSaleItemsCpe(this.document);
            if (!validate_restrict_sale_items_cpe.success)
                return this.$message.error(
                    validate_restrict_sale_items_cpe.message
                );

            if (this.document.items.length === 0)
                return this.$message.error("No tiene productos agregados.");

            if (this.generate_dispatch) {
                if (!this.form.dispatch_id) {
                    return this.$message.error(
                        "Debe seleccionar una guía base"
                    );
                } else {
                    this.document.dispatch_id = this.form.dispatch_id;
                }
            } else {
                this.form.dispatch_id = null;
            }

            let validate_payment_destination =
                await this.validatePaymentDestination();

            if (validate_payment_destination.error_by_item > 0 && !this.payed) {
                return this.$message.error(
                    validate_payment_destination.message
                );
            }

            let validate_payment_date = this.validatePaymentDate();
            // console.log(validate_payment_date)

            if (validate_payment_date.error_by_item > 0) {
                return this.$message.error(validate_payment_date.message);
            }

            this.loading_submit = true;

            this.document.exchange_rate_sale = 1;
            if (this.cash_id) {
                this.document.cash_id = this.cash_id;
            }
            if (this.payed) {
                this.document.payments = [];
            }

            //     console.log("🚀 ~ submit ~ this.document:", this.document)
            // return;
            this.document.sale_note_payment_id = this.currentPayment.id;
            await this.$http
                .post(`/${this.resource_documents}`, this.document)
                .then((response) => {
                    if (response.data.success) {
                        this.documentNewId = response.data.data.id;
                        if (payWithAdvance) {
                            this.form_cash_document.document_id =
                                response.data.data.id;
                            this.form_cash_document.advance_id = payWithAdvance;
                            this.saveAdvanceDocument();
                        }
                        this.showDialogDocumentOptions = true;
                        this.$http
                            .get(`/${this.resource}/changed/${this.recordId}`)
                            .then(() => {
                                this.$eventHub.$emit("reloadData");
                                // this.flag_generate = false
                            });
                        this.$http
                            .get(`/sale-note-payment/changed/${this.recordId}`)
                            .then(() => {
                                this.$eventHub.$emit("reloadData");
                                // this.flag_generate = false
                            });
                        this.resetDocument();

                        this.$emit("hasGeneratedDocument");
                        this.$emit("getData");
                        this.$emit("update:show", false);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error(error.response.data.message);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                    $.each($(".v-modal"), function (a, b) {
                        /* v-modal se le resta 5 z-index para que no se sobreponga en el modal*/
                        $(b).css("z-index", $(b).css("z-index") - 5);
                    });
                });
        },
        validatePaymentDates() {
            var valid = _.filter(this.document.payments, (item) => {
                return true;
            });

            return valid;
        },
        getPaymentsData(q) {
            let sale_note_payments = q.payments;

            if (
                this.form.payments !== undefined &&
                Array.isArray(this.form.payments)
            ) {
                let new_payments = [];

                this.form.payments.forEach((row) => {
                    const payment = { ...row };

                    if (
                        !_.some(this.payment_destinations, {
                            id: row.payment_destination_id,
                        })
                    ) {
                        payment.payment_destination_id = null;
                    }

                    new_payments.push(payment);
                });

                sale_note_payments = new_payments;
            }

            return sale_note_payments;
        },
        assignDocument() {
            let q = this.form.sale_note;
            // console.log(q);

            this.document.establishment_id = q.establishment_id;
            this.document.date_of_issue = moment().format("YYYY-MM-DD"); //q.date_of_issue
            this.document.date_of_due = moment().format("YYYY-MM-DD"); //q.date_of_issue
            this.document.time_of_issue = q.time_of_issue;
            this.document.customer_id = q.customer_id;
            this.document.currency_type_id = q.currency_type_id;
            this.document.purchase_order = null;
            this.document.exchange_rate_sale = q.exchange_rate_sale;
            this.document.total_prepayment = q.total_prepayment;
            this.document.total_charge = q.total_charge;
            this.document.total_discount = q.total_discount;
            this.document.total_exportation = q.total_exportation;
            this.document.total_free = q.total_free;
            this.document.total_taxed = q.total_taxed;
            this.document.total_unaffected = q.total_unaffected;
            this.document.total_exonerated = q.total_exonerated;
            this.document.total_igv = q.total_igv;
            this.document.total_igv_free = q.total_igv_free;
            this.document.total_base_isc = q.total_base_isc;
            this.document.total_isc = q.total_isc;
            this.document.total_base_other_taxes = q.total_base_other_taxes;
            this.document.total_other_taxes = q.total_other_taxes;
            this.document.total_taxes = q.total_taxes;
            this.document.total_value = q.total_value;
            this.document.subtotal = q.subtotal;
            this.document.total = q.total;
            this.document.operation_type_id = "0101";

            this.document.items = q.items;
            this.document.purchase_order = q.purchase_order || "";
            this.document.additional_information = q.observation || "";
            this.document.charges = q.charges;
            this.document.discounts =
                q.discounts && Object.keys(q.discounts).length
                    ? q.discounts
                    : [];

            this.document.attributes = [];
            this.document.guides = q.guides;
            this.document.actions = {
                format_pdf: "a4",
            };
            this.document.sale_note_id = this.form.id;
            // this.document.payments = q.payments;
            let { total_canceled, paid } = this.form;
            // this.payed = total_canceled || paid;

            this.document.total_canceled = total_canceled;
            this.document.seller_id = q.seller_id;
            this.document.user_id = q.user_id;
            this.document.fee = [];
            this.document.payment_condition_id = q.payment_condition_id;
            if (
                this.document.payment_condition_id === undefined ||
                this.document.payments.length > 0
            ) {
                this.document.payment_condition_id = "01";
            }
            if (this.document.payment_condition_id == undefined) {
                this.document.payment_condition_id = "01";
                this.clickAddPayment();
            }
            this.assignPlateNumberToItems(q);
            //console.log(this.document);
        },
        async assignPlateNumberToItems(sale_note) {
            if (sale_note.plate_number) {
                await this.document.items.forEach((item) => {
                    let empty_attributes = _.isEmpty(item.attributes);

                    if (empty_attributes) {
                        item.attributes = [];
                        let attribute = _.find(item.attributes, {
                            attribute_type_id: "7000",
                        });

                        if (!attribute) {
                            item.attributes.push({
                                attribute_type_id: "7000",
                                description:
                                    "Gastos Art. 37 Renta:  Número de Placa",
                                value: sale_note.plate_number,
                                start_date: null,
                                end_date: null,
                                duration: null,
                            });
                        }
                    }
                });
            }
        },
        async create() {
            this.prepayment_deduction = this.isPrepaymentDeduction;
            if (this.prepayment_deduction) {
                this.getDocumentsPrepayment();
            }
            this.initForm();
            let url = `/${this.resource}/option/tables`;
            if (this.recordId) {
                url = `/${this.resource}/option/tables/${this.recordId}`;
            }
            await this.$http.get(url).then((response) => {
                this.document.company_id = response.data.company_id;
                this.document.establishment = response.data.establishment_info;
                this.all_document_types = response.data.document_types_invoice;
                this.all_series = response.data.series;
                this.payment_destinations = response.data.payment_destinations;
                this.payment_method_types = response.data.payment_method_types;
                this.sellers = response.data.sellers;
                this.configuration = response.data.configuration;
                this.global_discount_types =
                    response.data.global_discount_types;

                // this.document.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null;
                // this.changeDocumentType();
            });

            await this.$http
                .get(`/${this.resource}/record/${this.recordId}`)
                .then((response) => {
                    this.form = response.data.data;
                    this.validateIdentityDocumentType();

                    this.assignDocument();
                    this.load_list_document_items = true;
                    this.titleDialog =
                        "Documento por anticipo - Nota de venta: " +
                        this.form.number_full;
                    if (this.isPrepaymentDeduction) {
                        this.titleDialog =
                            "Aplicación de anticipo - Nota de venta: " +
                            this.form.number_full;
                    }

                    let payment = this.currentPayment.total_payment || 0;
                    this.document.additional_information =
                        "Nota de venta: " + this.form.number_full;
                    if (this.form.purchase_order) {
                        this.document.additional_information +=
                            "\nOrden de compra: " + this.form.purchase_order;
                    }
                    this.getIgvByItem();
                    this.initialValueATPrepayment();
                    if (!this.isPrepaymentDeduction) {
                        this.calculatePercentage();
                        this.calculateNewPartialTotal(payment);
                        this.splitByQuantity();
                        this.changePriceAllItems();
                        this.changeHasPrepayment();
                    } else {
                        this.document.has_prepayment = false;
                        this.changePrepaymentDeduction();
                        this.calculateTotal();
                        console.log(this.document);
                    }
                });
            // this.getAdvance(this.document.customer_id);

            // await this.$http
            //     .get(`/${this.resource}/dispatches`)
            //     .then((response) => {
            //         this.dispatches = response.data;
            //     });
        },
        changePriceAllItems() {
            this.document.items.forEach((item, index) => {
                this.changeUnitPriceItem(item, index);
            });
        },
        getIgvByItem() {
            let { items } = this.document;
            let [item] = items.filter(
                (item) => item.affectation_igv_type_id === "10"
            );
            if (item) {
                this.percentage_igv = Number(item.percentage_igv);
            } else {
                let [item] = items;
                this.percentage_igv = Number(item.percentage_igv);
            }
        },
        changeDocumentType() {
            this.filterSeries();
        },
        searchDispatches(input) {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(() => {
                this.loading_search = true;
                this.$http
                    .get(`/${this.resource}/dispatches?input=${input}`)
                    .then((response) => {
                        this.dispatches = response.data;
                    })
                    .catch((error) => {
                        this.$message.error(error.response.data.message);
                    })
                    .then(() => {
                        this.loading_search = false;
                    });
            }, 600);
        },
        async validateIdentityDocumentType() {
            let identity_document_types = ["0", "1"];

            if (
                identity_document_types.includes(
                    this.form.sale_note.customer.identity_document_type_id
                )
            ) {
                this.document_types = _.filter(this.all_document_types, {
                    id: "03",
                });
            } else {
                this.document_types = this.all_document_types;
            }

            this.document.document_type_id =
                this.document_types.length > 0
                    ? this.document_types[0].id
                    : null;
            await this.changeDocumentType();
        },
        filterSeries() {
            this.document.series_id = null;
            this.series = _.filter(this.all_series, {
                document_type_id: this.document.document_type_id,
            });
            this.document.series_id =
                this.series.length > 0 ? this.series[0].id : null;
        },
        clickFinalize() {
            location.href = `/${this.resource}`;
        },
        clickNewQuotation() {
            this.clickClose();
        },
        clickClose() {
            this.$emit("update:show", false);
            this.initForm();
            // this.initDocument();

            this.resetDocument();
            // this.flag_generate = true
        },
        clickToPrint() {
            window.open(
                `/downloads/saleNote/sale_note/${this.form.external_id}`,
                "_blank"
            );
        },
    },
};
</script>
