<template>
    <el-dialog
        width="70%"
        @close="close"
        :close-on-click-modal="false"
        :visible="showDialog"
        @open="open"
        :title="title"
        top="2vh"
        v-loading="loading" 
    >
        <div class="row" v-if="!searchBySeries">
            <div class="col-md-6 col-lg-6 col-12">
                <el-select
                    id="select-width"
                    ref="selectBarcode"
                    slot="prepend"
                    v-model="form.item_id"
                    :loading="loading_search"
                    :remote-method="searchRemoteItems"
                    filterable
                    placeholder="Buscar producto"
                    popper-class="el-select-items"
                    remote
                    value-key="id"
                    @change="changeItem"
                >
                    <el-option
                        v-for="option in items"
                        :key="option.id"
                        :label="option.full_description"
                        :value="option.id"
                    ></el-option>
                </el-select>
            </div>
            <div class="col-md-3 col-lg-3 col-12">
                <el-input
                    v-model="form.quantity"
                    :disabled="!form.item_id"
                ></el-input>
            </div>
            <div class="col-md-3 col-lg-3 col-12">
                <el-button type="primary" @click="insertProduct">
                    <i class="fas fa-plus"></i>
                </el-button>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-12">
                <el-input
                    v-model="form.series"
                    @input="searchRemoteSeries"
                    placeholder="Ingrese la serie del producto"
                ></el-input>
            
            </div>
        </div>
        <div class="mt-1 d-flex justify-content-start">
            <div class="col-md-3 col-lg-3 col-12">
                <el-checkbox v-model="searchBySeries"
                    >Buscar producto seriado</el-checkbox
                >
            </div>
        </div>

        <div class="row mt-2">
            <div class="table-responsive" style="max-height: 350px">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(item, idx) in itemsSelected">
                            <tr :key="idx">
                                <td>{{ item.full_description }}</td>
                                <td
                                    :class="{
                                        'text-danger':
                                            item.quantity != item.stock,
                                    }"
                                >
                                    {{ item.quantity }}
                                </td>
                                <td>{{ item.stock }}</td>
                                <td>
                                    <el-button
                                        type="danger"
                                        size="mini"
                                        @click="removeItem(item.id)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </el-button>
                                </td>
                            </tr>
                            <template v-if="item.lots && item.lots.length > 0">
                                <tr :key="`${idx}-x`">
                                    <td colspan="4">
                                        <el-collapse
                                            :key="idx"
                                            v-model="item.name"
                                        >
                                            <el-collapse-item
                                                :name="`${item.name}`"
                                            >
                                                <template slot="title">
                                                    <small class="text-primary"
                                                        >ver series</small
                                                    >
                                                </template>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Serie</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(
                                                                lot, idx
                                                            ) in item.lots"
                                                            :key="idx"
                                                        >
                                                            <td>{{ lot }}</td>
                                                            <td>
                                                                <el-button
                                                                    type="danger"
                                                                    size="mini"
                                                                    @click="
                                                                        removeSeries(
                                                                            item.id,
                                                                            idx
                                                                        )
                                                                    "
                                                                >
                                                                    <i
                                                                        class="fas fa-trash"
                                                                    ></i>
                                                                </el-button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </el-collapse-item>
                                        </el-collapse>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <el-input
                    v-model="observation"
                    type="textarea"
                    rows="2"
                    placeholder="Observación"
                ></el-input>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button
                type="warning"
                v-if="itemsSelected.length > 0"
                @click="validateInventory"
                >Validar inventario</el-button
            >
            <el-button
                type="success"
                v-if="validateItems.length > 0"
                @click="showResultValidation = true"
                >Ver validación</el-button
            >
            <el-button
                type="primary"
                v-if="isValidate"
                @click="saveValidation"
                >Guardar</el-button>
        </span>
        <show-validate
            :items="validateItems"
            :showDialog.sync="showResultValidation"
        ></show-validate>
    </el-dialog>
</template>
<style scoped>
.el-collapse {
    border: none !important;
}
.el-collapse-item__header {
    height: 10px !important;
    border: none !important;
}
</style>
<script>

import ShowValidate from "./show_validation.vue";
export default {
    components: {
        ShowValidate,
    },
    props: ["warehouse_id", "showDialog", "warehouse"],
    data() {
        return {
            loading:false,
            isValidate: false,
            itemsEmpty: [],
            resource: "inventory/validate",
            searchBySeries: false,
            input: "",
            observation: "",
            timer: null,
            title: "Validar inventario",
            quantity: 0,
            items: [],
            itemsSelected: [],
            loading_search: false,
            form: {
                item_id: null,
                quantity: 0,
                series: "",
            },
            validateItems: [],
            showResultValidation:false,
        };
    },
    watch: {
        isValidate(value) {
            if (!value) {
                    this.validateItems = [];
            }
        },
    },
    computed: {
    
        placeholder() {
            return this.searchBySeries
                ? "Ingrese la serie del producto"
                : "Ingrese el nombre / codigo del producto";
        },
    },
    methods: {
        async saveValidation(){
            let { validateItems, observation } = this;
            let data = {
                validateItems,
                observation,
                warehouse_id: this.warehouse_id,
            };
            this.loading = true;
            await this.$http
                .post(`/${this.resource}/save-validation`, data)
                .then((response) => {
                    let data = response.data;
                    this.$message.success(data.message);
                    this.$emit("update:showDialog", false);
                })
                .catch((error) => {
                    let data = error.response.data;
                    this.$message.error(data.message);
                }).finally(() => {
                    this.loading = false;
                });
        },
        validateInventory() {
            let { itemsSelected } = this;
            if (itemsSelected.length > 0) {
                let data = {
                    itemsSelected,
                    warehouse_id: this.warehouse_id,
                };
                this.loading = true;
                this.$http
                    .post(`/${this.resource}/validate-items`, data)
                    .then((response) => {
                        let data = response.data;
                        
                        this.$message.success(data.message);
                        this.isValidate = true;
                        this.validateItems = data.itemsNotCount.map((item) => {
                            return {
                                ...item,
                                showLots: false,
                            };
                        })
                        this.showResultValidation = true;
                        // this.$emit("update:showDialog", false);
                    })
                    .catch((error) => {
                        let data = error.response.data;
                        this.$message.error(data.message);
                    }).finally(() => {
                        this.loading = false;
                    })
                    ;
            } else {
                this.$message.error("Debe ingresar al menos un producto");
            }
        },
        removeItem(id) {
            this.itemsSelected = this.itemsSelected.filter(
                (item) => item.id !== id
            );
            this.isValidate = false;
        },
        removeSeries(itemId, index) {
            let itemSelected = this.itemsSelected.find(
                (item) => item.id === itemId
            );
            itemSelected.lots.splice(index, 1);
            itemSelected.quantity -= 1;
            this.itemsSelected = this.itemsSelected.filter(
                (item) => item.id !== itemId
            );
            this.itemsSelected.unshift(itemSelected);
            this.isValidate = false;
        },
        initForm() {
            this.form = {
                item_id: null,
                quantity: 0,
                series: "",
            };
        },
        changeItem() {},
        insertSeries() {
            let { item_id } = this.form;

            if (item_id) {
                let exists = this.itemsSelected.find(
                    (item) => item.id === item_id
                );
                if (exists) {
                    let itemSelected = this.itemsSelected.find(
                        (item) => item.id === item_id
                    );
                    let existsSerie = itemSelected.lots.find(
                        (lot) => lot === this.form.series
                    );
                    if (existsSerie) {
                        this.$message.error("La serie ya fue ingresada");
                        return;
                    }
                    itemSelected.lots.unshift(this.form.series);
                    itemSelected.quantity += 1;
                    this.$message.success("Serie ingresada correctamente");
                    this.itemsSelected = this.itemsSelected.filter(
                        (item) => item.id !== item_id
                    );
                    this.itemsSelected.unshift(itemSelected);
                    this.isValidate = false;
                } else {
                    let item = this.items.find((item) => item.id === item_id);
                    let itemSelected = {
                        id: item.id,
                        full_description: item.full_description,
                        quantity: 1,
                        lots: [this.form.series],
                        name: Math.random().toString(36).substring(7),
                        stock: item.stock,
                    };
                    this.itemsSelected.unshift(itemSelected);
                    this.isValidate = false;
                }
                this.initForm();
            } else {
                this.$message.error("Debe ingresar una serie");
            }
        },
        insertProduct() {
            let { item_id, quantity } = this.form;
            if (item_id && quantity) {
                let item = this.items.find((item) => item.id === item_id);
                let exists = this.itemsSelected.find(
                    (item) => item.id === item_id
                );
                if (exists) {
                    return this.$message.error("El producto ya fue ingresado");
                }
                let itemSelected = {
                    id: item.id,
                    full_description: item.full_description,
                    quantity,
                    stock: item.stock,
                };
                this.itemsSelected.unshift(itemSelected);
                this.isValidate = false;
                this.initForm();
            } else {
                this.$message.error(
                    "Debe seleccionar un producto y una cantidad"
                );
            }
        },
        open() {
            let { description } = this.warehouse;
            this.title = `Validar inventario del ${description}`;
            this.form = {
                item_id: null,
                quantity: 0,
                series: "",
            };
            this.items = [];
            this.itemsSelected = [];
            this.validateItems = [];
            this.isValidate = false;
            this.showResultValidation = false;


        },
        close() {
            this.$emit("update:showDialog", false);
        },
        debounceSearch() {
            // this.$emit("debounceSearch", this.input);
        },
        async searchRemoteSeries(input) {
            if (input.length > 2) {
                this.loading_search = true;
                const params = {
                    input: input,
                    warehouse_id: this.warehouse_id,
                };
                await this.$http
                    .get(`/${this.resource}/search-series-validate/`, {
                        params,
                    })
                    .then((response) => {
                        let items = response.data.items;
                        if (items.length > 0) {
                            if (items.length !== 1) {
                                this.$message.error(
                                    "Se encontraron varios resultados"
                                );
                            } else {
                                this.items = items;
                                this.form.item_id = items[0].id;
                                this.insertSeries();
                                this.form.series = "";
                            }
                        } else {
                            this.$message.error("No se encontraron resultados");
                        }
                        this.loading_search = false;
                    });
            }
        },
        async searchRemoteItems(input) {
            if (input.length > 2) {
                this.loading_search = true;
                const params = {
                    input: input,
                    warehouse_id: this.warehouse_id,
                };
                await this.$http
                    .get(`/${this.resource}/search-items-validate/`, { params })
                    .then((response) => {
                        this.items = response.data.items;
                        this.loading_search = false;
                    });
            }
        },
    },
};
</script>
