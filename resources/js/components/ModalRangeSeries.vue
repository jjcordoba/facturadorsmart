<template>
    <el-dialog
        :title="`Multiple rangos cantidad: ${toAttend} - Cantidad ingresada: ${quantity}`"
        :visible="showDialog"
        @open="open"
        @close="close"
        width="50%"
    >
        <div class="row mt-2">
            <div class="col-md-4">
                <label>Inicio</label>
                <el-input
                    v-model="start_range"
                    placeholder="Inicio"
                    style="width: 100%"
                    type="number"
                ></el-input>
            </div>
            <div class="col-md-4">
                <label>Fin</label>
                <el-input
                    v-model="end_range"
                    placeholder="Fin"
                    style="width: 100%"
                    type="number"
                ></el-input>
            </div>
            <div class="col-md-4">
                <br />

                <el-button type="primary" @click="addRange">Agregar</el-button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <el-table
                    :data="rangeSeries"
                    style="width: 100%"
                    stripe
                    border
                    height="250"
                >
                    <el-table-column
                        prop="start_range"
                        label="Inicio"
                    ></el-table-column>
                    <el-table-column
                        prop="end_range"
                        label="Fin"
                    ></el-table-column>
                    <el-table-column label="Acciones">
                        <template slot-scope="scope">
                            <div class="d-flex justify-content-center">
                                <el-button
                                    type="danger"
                                    size="mini"
                                    @click="rangeSeries.splice(scope.$index, 1)"
                                >
                                    <i class="el-icon-delete"></i>
                                </el-button>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="primary" @click="saveRange"> Aceptar </el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: {
        showDialog: {
            type: Boolean,
            default: false,
        },
        toAttend: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            series: [],
            start_range: "",
            end_range: "",
            rangeSeries: [],

        };
    },
    computed: {
        quantity() {
            return this.rangeSeries.reduce((acc, range) => {
                return acc + (range.end_range - range.start_range + 1);
            }, 0);
        },
    },
    methods: {
        open() {
            this.rangeSeries = [];
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        saveRange() {
            let series = this.rangeSeries.reduce((acc, range) => {
                for (let i = range.start_range; i <= range.end_range; i++) {
                    acc.push(i);
                }
                return acc;
            }, []);
            if(series.length !== this.toAttend){
                this.$message.error("La cantidad de series ingresadas no coincide con la cantidad solicitada");
                return;
            }
            this.$emit("setRange", series);
            this.close();
        },
        validate() {
            if (this.rangeSeries.length == 0) {
                this.$message.error("Debe agregar al menos un rango");
                return false;
            }
            return true;
        },
        // createRange(start,end){
        //     let range = [];
        //     for (let i = start; i <= end; i++) {
        //         range.push(i);
        //     }
        //     return range;
        // },
         checkRangeSeries() {
            let pass = true;
            let start_range = parseInt(this.start_range);
            let end_range = parseInt(this.end_range);

            if (isNaN(start_range) || isNaN(end_range)) {
                pass = false;
                this.$message.error("Debe ingresar la serie inicial y final");
            }
            if (!start_range || !end_range) {
                pass = false;
                this.$message.error("Debe ingresar la serie inicial y final");
            }
            if (start_range > end_range) {
                pass = false;
                this.$message.error(
                    "La serie inicial no puede ser mayor a la serie final"
                );
            }
            // let quantity = parseInt(this.toAttend);
            // let total = start_range + quantity - 1;
            // if (total !== end_range) {
            //     pass = false;
            //     this.$message.error(
            //         "La cantidad de series ingresadas no coincide con la cantidad solicitada"
            //     );
            // }

            return pass;
        },
        checkIfExistInRange(start, end) {
            let exist = false;
            this.rangeSeries.forEach((range) => {
                if (
                    (start >= Number(range.start_range) &&
                        start <= Number(range.end_range)) ||
                    (end >= Number(range.start_range) &&
                        end <= Number(range.end_range))
                ) {
                    exist = true;
                }
            });
            return exist;
        },
        addRange() {
            if (!this.checkRangeSeries()) {
                return;
            }
            if (this.checkIfExistInRange(this.start_range, this.end_range)) {
                this.$message.error("Alguna serie del rango ya fue agregada");
                return;
            }
            this.rangeSeries.push({
                start_range: Number(this.start_range),
                end_range: Number(this.end_range),
            });
            this.start_range = "";
            this.end_range = "";
        },
    },
};
</script>
