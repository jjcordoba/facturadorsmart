<template>

    <el-dialog
    title="Cuotas de Crédito"
    :visible="showDialog"
    @open="open"
    @close="close"
    append-to-body
    :close-on-click-modal="false"
    >
    <div class="row mt-2">
        <div class="col-md-6">
            <label for="total">
                Total
            </label>
                <el-input
                    v-model="total"
                    readonly
                    placeholder="Total"
                ></el-input>
        </div>
        <div class="col-md-6">
            <label for="total">
                Por asignar
            </label>
                <el-input
                type="number"
                    v-model="remaining"
                ></el-input>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-4">
            <label>
                Cuota
            </label>
            <el-input
            type="number"
                v-model="fee"
                placeholder="Cuota"
            ></el-input>
        </div>
        <div class="col-md-4">
            <label>Fecha vencimiento</label>
            <el-date-picker
                v-model="date"
                type="date"
                placeholder="Seleccione una fecha"
                value-format="yyyy-MM-dd"
                format="yyyy-MM-dd"
            ></el-date-picker>
        </div>
        <div class="col-md-4">
            <br>
            <el-button type="primary"
            @click="addFee"
            >Agregar</el-button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cuota</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(fee,index) in fees" :key="fee.id">
                        <td>{{index+1}}</td>
                        <td>{{fee.fee}}</td>
                        <td>{{fee.date}}</td>
                        <td>
                            <el-button type="danger" size="mini"
                            @click="fees.splice(index,1)"
                            >
                                <i class="fas fa-trash"></i>
                            </el-button>
                        </td>
                    </tr>   
                </tbody>
            </table>
        </div>
    </div>
    <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">Cancelar</el-button>
        <el-button type="primary" @click="saveFees">Guardar</el-button>
    </span>
    </el-dialog>
</template>

<script>
import moment from "moment";
export default {
    props: ["showDialog","total"],
    data() {
        return {
            fees: [],
            fee:0,
            date: moment().format("YYYY-MM-DD"),
        };
    },
    computed: {
        remaining(){
            let total = this.total;
            let sum = 0;
            this.fees.forEach(fee => {
                sum += Number(fee.fee);
            });
            return total - sum;
        }
    },  
    methods: {
        saveFees(){
            if(this.remaining > 0){
                this.$message.error("Aun hay saldo por asignar");
                return;
            }
            if(this.fees.length == 0){
                this.$message.error("No hay cuotas asignadas");
                return;
            }
            if(this.remaining < 0){
                this.$message.error("El saldo asignado es mayor al total");
                return;
            }
            this.$emit("updateFeeds",this.fees);
            this.close();
        },
        open(){},
        close(){
            this.$emit("update:showDialog",false);
        },
        addFee(){
            if(this.fee == 0){
                this.$message.error("La cuota no puede ser 0");
                return;
            }
            if(this.remaining < this.fee){
                this.$message.error("La cuota no puede ser mayor al saldo");
                return;
            }
            this.fees.push({
                fee: this.fee,
                date: this.date,
            });
            this.fee = 0;
            this.date = moment().format("YYYY-MM-DD");

        },
        async getFees() {
            try {
                const response = await axios.get("/api/fees");
                this.fees = response.data.data;
            } catch (error) {
                console.log(error);
            }
        },
    },
};
</script>
