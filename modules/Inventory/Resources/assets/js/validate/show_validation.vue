<template>
    <el-dialog
        :visible="showDialog"
        title="Resultado de validación"
        width="40%"
        @close="close"
        :lock-scroll="false"
        append-to-body
    >
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="text-end">Contabilizado</th>
                        <th class="text-end">En stock</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="item in items">
                        <tr :key="item.id">
                            <td>
                                <template
                                    v-if="item.lots && item.lots.length > 0"
                                >
                                    <label role="button"
                                    @click="showLots(item.id)"
                                    >
                                        {{ item.full_description }}
                                        <i :class="item.showLots ? 'el-icon-arrow-up' : 'el-icon-arrow-down'"></i>
                                    </label>
                                </template>
                                <template v-else>
                                    {{ item.full_description }}
                                </template>
                            </td>
                            <td class="text-end">{{ item.quantity }}</td>
                            <td class="text-end">{{ item.stock }}</td>
                        </tr>
                        <template v-if="item.lots && item.lots.length > 0 && item.showLots">
                            <tr
                                :key="`lot-${item.id}-${idx}`"
                                v-for="(lot, idx) in item.lots"
                            >
                                <td>{{ lot.series }}</td>
                                <td class="text-end">{{ lot.quantity }}</td>
                                <td></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cerrar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "items"],
    data() {
        return {
            warehouses: [],
        };
    },
    methods: {
        showLots(itemId) {
            const item = this.items.find((item) => item.id === itemId);
            item.showLots = !item.showLots;
        },
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

<style scoped>
.table-wrapper {
    max-height: 400px; /* Puedes ajustar esta altura según sea necesario */
    overflow-y: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    position: sticky;
    top: 0;
    background-color: #fff; /* Fondo blanco para que las cabeceras no se mezclen con el contenido */
    z-index: 1;
}

th,
td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.text-end {
    text-align: right;
}

.dialog-footer {
    text-align: right;
}
</style>
