<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @open="create"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{
                                'has-danger': errors.identity_document_type_id,
                            }"
                        >
                            <label class="control-label"
                                >Tipo Doc. Identidad</label
                            >
                            <el-select
                                v-model="transport.identity_document_type_id"
                                filterable
                                popper-class="el-select-identity_document_type"
                            >
                                <el-option
                                    v-for="option in identity_document_types"
                                    :key="option.id"
                                    :value="option.id"
                                    :label="option.description"
                                ></el-option>
                            </el-select>
                            <small
                                class="text-danger"
                                v-if="errors.identity_document_type_id"
                                v-text="errors.identity_document_type_id[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            class="form-group"
                            :class="{
                                'has-danger': errors.number_identity_document,
                            }"
                        >
                            <label class="control-label"
                                >Número documento</label
                            >
                            <template
                                v-if="
                                    transport.identity_document_type_id === '1'
                                "
                            >
                                <x-input-service
                                    v-model="transport.number_identity_document"
                                    :value="''"
                                    :identity_document_type_id="
                                        transport.identity_document_type_id
                                    "
                                    @search="searchNumber"
                                ></x-input-service>
                            </template>
                            <template v-else>
                                <el-input
                                    v-model="transport.number_identity_document"
                                    :maxlength="maxLength"
                                />
                            </template>

                            <small
                                class="text-danger"
                                v-if="errors.number_identity_document"
                                v-text="errors.number_identity_document[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.passenger_fullname }"
                        >
                            <label class="control-label"
                                >Nombres y Apellidos</label
                            >
                            <el-input
                                v-model="transport.passenger_fullname"
                            ></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.passenger_fullname"
                                v-text="errors.passenger_fullname[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.passenger_age }"
                        >
                            <label class="control-label">Edad</label>
                            <el-input
                                v-model="transport.passenger_age"
                            ></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.passenger_age"
                                v-text="errors.passenger_age[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.bus_number }"
                        >
                            <label class="control-label">N° Bus</label>
                            <el-input v-model="transport.bus_number"></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.bus_number"
                                v-text="errors.bus_number[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.seat_number }"
                        >
                            <label class="control-label">N° Asiento</label>
                            <el-input
                                v-model="transport.seat_number"
                            ></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.seat_number"
                                v-text="errors.seat_number[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.passenger_manifest }"
                        >
                            <label class="control-label"
                                >Manifiesto pasajeros</label
                            >
                            <el-input
                                v-model="transport.passenger_manifest"
                            ></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.passenger_manifest"
                                v-text="errors.passenger_manifest[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.start_date }"
                        >
                            <label class="control-label"
                                >F. Inicio programado</label
                            >
                            <el-date-picker
                                v-model="transport.start_date"
                                type="date"
                                value-format="yyyy-MM-dd"
                                :clearable="false"
                            ></el-date-picker>
                            <small
                                class="text-danger"
                                v-if="errors.start_date"
                                v-text="errors.start_date[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.start_time }"
                        >
                            <label class="control-label"
                                >H. Inicio programado</label
                            >
                            <el-time-picker
                                v-model="transport.start_time"
                                value-format="HH:mm:ss"
                            >
                            </el-time-picker>
                            <small
                                class="text-danger"
                                v-if="errors.start_time"
                                v-text="errors.start_time[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-lg-3">
                        <div
                            class="form-group"
                            :class="{
                                'has-danger': errors.agency_origin_id,
                            }"
                        >
                            <label class="control-label">Agencia origen</label>
                            <el-select
                                v-model="transport.agency_origin_id"
                                filterable
                                popper-class="el-select-agency"
                                @change="setOriginInfo"
                            >
                                <el-option
                                    v-for="option in agencies"
                                    :key="option.id"
                                    :value="option.id"
                                    :label="option.description"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.origin_district_id }"
                        >
                            <label class="control-label">Ubigeo Origen</label>
                            <el-cascader
                                :options="locations"
                                v-model="transport.origin_district_id"
                                filterable
                            ></el-cascader>
                            <small
                                class="text-danger"
                                v-if="errors.origin_district_id"
                                v-text="errors.origin_district_id[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.origin_address }"
                        >
                            <label class="control-label"
                                >Dirección detallada de origen</label
                            >
                            <el-input
                                v-model="transport.origin_address"
                            ></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.origin_address"
                                v-text="errors.origin_address[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div
                            class="form-group"
                            :class="{
                                'has-danger': errors.agency_destination_id,
                            }"
                        >
                            <label class="control-label">Agencia destino</label>
                            <el-select
                                v-model="transport.agency_destination_id"
                                filterable
                                popper-class="el-select-agency"
                                @change="setDestinationInfo"
                            >
                                <el-option
                                    v-for="option in agencies"
                                    :key="option.id"
                                    :value="option.id"
                                    :label="option.description"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div
                            class="form-group"
                            :class="{
                                'has-danger': errors.destinatation_district_id,
                            }"
                        >
                            <label class="control-label">Ubigeo Destino</label>
                            <el-cascader
                                :options="locations"
                                v-model="transport.destinatation_district_id"
                                filterable
                            ></el-cascader>
                            <small
                                class="text-danger"
                                v-if="errors.destinatation_district_id"
                                v-text="errors.destinatation_district_id[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            class="form-group"
                            :class="{
                                'has-danger': errors.destinatation_address,
                            }"
                        >
                            <label class="control-label"
                                >Dirección detallada de llegada</label
                            >
                            <el-input
                                v-model="transport.destinatation_address"
                            ></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.destinatation_address"
                                v-text="errors.destinatation_address[0]"
                            ></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end mt-4">
                <el-button @click.prevent="close(true)">Cancelar</el-button>
                <el-button
                    type="primary"
                    native-type="submit"
                    :loading="loading_submit"
                    >Guardar</el-button
                >
            </div>
        </form>
    </el-dialog>
</template>

<script>
import { serviceNumber } from "../../../../../../../resources/js/mixins/functions";
export default {
    props: ["showDialog", "transportSaved"],
    mixins: [serviceNumber],
    data() {
        return {
            transport: {
                identity_document_type_id: "1",
                number_identity_document: "",
                passenger_fullname: null,
                passenger_age: null,
                bus_number: null,
                seat_number: null,
                passenger_manifest: null,
                start_date: null,
                start_time: null,
                agency_origin_id: null,
                origin_district_id: null,
                origin_address: null,
                agency_destination_id: null,
                destinatation_district_id: null,
                destinatation_address: null,
            },
            titleDialog: "Datos para transporte de pasajeros",
            loading_submit: false,
            errors: {},
            form: {},
            resource: "bussiness_turns",
            company: {},
            configuration: {},
            identity_document_types: [],
            locations: [],
            agencies: [],
        };
    },
    computed: {
        maxLength: function () {
            if (this.transport.identity_document_type_id === "1") {
                return 8;
            } else {
                return 12;
            }
        },
    },
    async created() {
        await this.$http
            .get(`/${this.resource}/tables/transports`)
            .then((response) => {
                this.agencies = response.data.agencies;

                this.identity_document_types =
                    response.data.identity_document_types;
                this.locations = response.data.locations;
            });
    },
    methods: {
        initTransport() {
            this.transport = {
                identity_document_type_id: "1",
                number_identity_document: "",
                passenger_fullname: null,
                passenger_age: null,
                bus_number: null,
                seat_number: null,
                passenger_manifest: null,
                start_date: null,
                start_time: null,
                agency_origin_id: null,
                origin_district_id: null,
                origin_address: null,
                agency_destination_id: null,
                destinatation_district_id: null,
                destinatation_address: null,
            };
        },
        setOriginInfo() {
            let agency = this.agencies.find(
                (agency) => agency.id === this.transport.agency_origin_id
            );
            if (agency) {
                this.transport.origin_district_id = agency.ubigeo;
                this.transport.origin_address = agency.address;
            }
        },

        setDestinationInfo() {
            let agency = this.agencies.find(
                (agency) => agency.id === this.transport.agency_destination_id
            );
            if (agency) {
                this.transport.destinatation_district_id = agency.ubigeo;
                this.transport.destinatation_address = agency.address;
            }
        },
        searchNumber(data) {
            this.transport.passenger_fullname = data.name;
            this.$forceUpdate();
        },
        create() {
            this.errors = {};
            for (const key in this.transportSaved) {
                if (this.transport.hasOwnProperty(key)) {
                    this.transport[key] = this.transportSaved[key];
                }
            }
        },
        submit() {
            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}/validate_transports`, this.transport)
                .then((response) => {
                    if (response.data.success) {
                        this.$emit("addDocumentTransport", this.transport);
                        this.close(false);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                        // console.log(error.response.data)
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        close(flag) {
            if (flag) this.$emit("addDocumentTransport", {});
            this.initTransport();
            this.errors = {};
            this.$emit("update:showDialog", false);
        },
    },
};
</script>
