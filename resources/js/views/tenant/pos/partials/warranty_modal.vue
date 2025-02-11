<template>
    <el-dialog
        :visible="showDialog"
        @open="open"
        @close="close"
        title="Prestamos con garantía"
        append-to-body
        :close-on-click-modal="false"
    >
        <div class="row mt-2">
            <div class="col-md-4">
                <label>Cantidad</label>
                <el-input v-model="form.quantity" type="number"></el-input>
            </div>
            <div class="col-md-4">
                <label for="amount">Monto dinero</label>
                <el-input v-model="form.amount" type="number"></el-input>
            </div>
            <div class="col-md-4">
                <label for="comments">Comentarios</label>
                <el-input v-model="form.comments"></el-input>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="primary" @click="save">Guardar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog"],
    data() {
        return {
            form: {},
            itemService:null,
        };
    },
    methods: {
        getItemService(){
            this.$http.get(`/pos/get-item-service`)
            .then((response) => {
                let {data:{data}} = response;
                this.itemService = data[0];
                console.log("🚀 ~ .then ~ this.itemService:", this.itemService)
            })
        },
        open() {
            this.form = {
                quantity: 1,
                amount: 0,
                comments: "",
            };
            this.getItemService();
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        save() {
                let {quantity, amount,comments} = this.form;

                if(quantity == 0 || amount == 0){
                    this.$message.error("La cantidad y el monto no pueden ser 0");
                    return;
                }
                if(comments == ""){
                    this.$message.error("El campo comentarios es obligatorio");
                    return;
                }
                let warranty = {
                    quantity,
                    amount,
                    comments
                };
                this.itemService.aux_quantity = this.form.quantity;
                this.itemService.quantity = this.form.quantity;
                this.itemService.sale_unit_price = this.form.amount / this.form.quantity;
                this.itemService.description = this.form.comments;
                this.itemService.has_igv = true;
                this.itemService.warranty = warranty;
                this.$emit("addItem", this.itemService);
                console.log("🚀 ~ save ~ this.itemService:", this.itemService)
                this.close();

        },
    },
};
</script>
