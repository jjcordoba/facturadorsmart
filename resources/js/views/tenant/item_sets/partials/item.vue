<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @open="create"
        @close="close"
        append-to-body
        v-loading="loading"
    >
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8 col-lg-8 col-xl-8 col-sm-8">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.individual_item_id }"
                        >
                            <label class="control-label"> Producto </label>
                            <el-select
                                v-model="form.individual_item_id"
                                @change="changeItem"
                                filterable
                                placeholder="Buscar"
                            >
                                <el-option
                                    v-for="option in individual_items"
                                    :key="option.id"
                                    :value="option.id"
                                    :label="option.full_description"
                                ></el-option>
                            </el-select>

                            <small
                                class="text-danger"
                                v-if="errors.individual_item_id"
                                v-text="errors.individual_item_id[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.quantity }"
                        >
                            <label class="control-label">Cantidad</label>
                            <el-input-number
                                v-model="form.quantity"
                                :min="0.01"
                            ></el-input-number>
                            <small
                                class="text-danger"
                                v-if="errors.quantity"
                                v-text="errors.quantity[0]"
                            ></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end mt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button
                    type="primary"
                    native-type="submit"
                    v-if="form.individual_item_id"
                    >Agregar</el-button
                >
            </div>
        </form>
    </el-dialog>
</template>
<style>
.el-select-dropdown {
    max-width: 80% !important;
    margin-right: 5% !important;
}
</style>
<script>
export default {
    props: ["showDialog", "warehouse_id"],
    data() {
        return {
            titleDialog: "Agregar Producto",
            resource: "item-sets",
            errors: {},
            form: {},
            individual_items: [],
            loading: false,
        };
    },
    created() {
        this.initForm();

        // this.$http.get(`/${this.resource}/item/tables`).then((response) => {
        //     this.individual_items = response.data.individual_items;
        // });
    },
    methods: {
        getIndividualItems() {
            try {
                this.loading = true;
                let url = `/${this.resource}/item/tables${
                    this.warehouse_id
                        ? `?warehouse_id=${this.warehouse_id}`
                        : ""
                }`;
                this.$http.get(url).then((response) => {
                    this.individual_items = response.data.individual_items;
                });
            } catch (e) {
                console.log(e);
            } finally {
                this.loading = false;
            }
        },
        initForm() {
            this.errors = {};

            this.form = {
                individual_item_id: null,
                sale_unit_price: 0,
                purchase_unit_price: 0,
                quantity: 1,
                full_description: null,
            };
        },
        create() {
            this.getIndividualItems();
        },
        close() {
            this.initForm();
            this.$emit("update:showDialog", false);
        },
        changeItem() {
            let item = _.find(this.individual_items, {
                id: this.form.individual_item_id,
            });
            this.form.sale_unit_price = item.sale_unit_price;
            this.form.full_description = item.full_description;
            this.form.purchase_unit_price = item.purchase_unit_price;
        },
        async clickAddItem() {
            this.$emit("add", this.form);
            this.initForm();
        },
    },
};
</script>
