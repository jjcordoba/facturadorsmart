<?php


namespace Modules\Optometry\Models;

use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\SystemIscType;
use App\Models\Tenant\ModelTenant;
use App\Traits\AttributePerItems;
use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Models\Warehouse;
use Modules\Optometry\Models\OptometryService;

/**
 * Class OptometryServiceItem
 *
 * @property int         $id
 * @property int|null    $optometry_services_id
 * @property int|null    $item_id
 * @property string|null $item
 * @property float|null  $quantity
 * @property float|null  $unit_value
 * @property string|null $affectation_igv_type_id
 * @property float|null  $total_base_igv
 * @property float|null  $percentage_igv
 * @property float|null  $total_igv
 * @property string|null $system_isc_type_id
 * @property float|null  $total_base_isc
 * @property float|null  $percentage_isc
 * @property float|null  $total_isc
 * @property float|null  $total_base_other_taxes
 * @property float|null  $percentage_other_taxes
 * @property float|null  $total_other_taxes
 * @property float|null  $total_plastic_bag_taxes
 * @property float|null  $total_taxes
 * @property string|null $price_type_id
 * @property float|null  $unit_price
 * @property float|null  $total_value
 * @property float|null  $total_charge
 * @property float|null  $total_discount
 * @property float|null  $total
 * @property string|null $attributes
 * @property string|null $discounts
 * @property string|null $charges
 * @property mixed       $additional_information
 * @property int|null    $warehouse_id
 * @property string|null $name_product_pdf
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @mixin ModelTenant
 */
class OptometryServiceItem extends ModelTenant
{
    use AttributePerItems;
    use UsesTenantConnection;


    protected $casts = [
        'quantity' => 'float',
        'unit_value' => 'float',
        'total_base_igv' => 'float',
        'percentage_igv' => 'float',
        'total_igv' => 'float',
        'total_base_isc' => 'float',
        'percentage_isc' => 'float',
        'total_isc' => 'float',
        'total_base_other_taxes' => 'float',
        'percentage_other_taxes' => 'float',
        'total_other_taxes' => 'float',
        'total_plastic_bag_taxes' => 'float',
        'total_taxes' => 'float',
        'unit_price' => 'float',
        'total_value' => 'float',
        'total_charge' => 'float',
        'total_discount' => 'float',
        'total' => 'float',
        'warehouse_id' => 'int',

    ];
    protected $fillable = [
        'optometry_services_id',
        'item_id',
        'item',
        'quantity',
        'unit_value',
        'affectation_igv_type_id',
        'total_base_igv',
        'percentage_igv',
        'total_igv',
        'system_isc_type_id',
        'total_base_isc',
        'percentage_isc',
        'total_isc',
        'total_base_other_taxes',
        'percentage_other_taxes',
        'total_other_taxes',
        'total_plastic_bag_taxes',
        'total_taxes',
        'price_type_id',
        'unit_price',
        'total_value',
        'total_charge',
        'total_discount',
        'total',
        'attributes',
        'discounts',
        'charges',
        'additional_information',
        'warehouse_id',
        'name_product_pdf'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function (self $item) {
            $document = $item->document;
            if ($document !== null && empty($item->warehouse_id)) {
                $warehouse = Warehouse::find($document->establishment_id);
                if ($warehouse !== null) {
                    $item->warehouse_id = $document->establishment_id;
                }
            }
            if (is_array(($item->item))) {
                // debe ser string
                $item->item = json_encode($item->item);
            }
            if (is_array(($item->additional_information))) {
                // Debe ser string
                $item->additional_information = implode('|', $item->additional_information);
            }
        });
    }

    /**
     * @return int|null
     */
    public function getOptometryServicesId(): ?int
    {
        return $this->optometry_services_id;
    }

    /**
     * @param int|null $optometry_services_id
     *
     * @return OptometryServiceItem
     */
    public function setOptometryServicesId(?int $optometry_services_id): OptometryServiceItem
    {
        $this->optometry_services_id = $optometry_services_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getItemId(): ?int
    {
        return $this->item_id;
    }

    /**
     * @param int|null $item_id
     *
     * @return OptometryServiceItem
     */
    public function setItemId(?int $item_id): OptometryServiceItem
    {
        $this->item_id = $item_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getItem(): ?string
    {
        return $this->item;
    }

    /**
     * @param string|null $item
     *
     * @return OptometryServiceItem
     */
    public function setItem(?string $item): OptometryServiceItem
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    /**
     * @param float|null $quantity
     *
     * @return OptometryServiceItem
     */
    public function setQuantity(?float $quantity): OptometryServiceItem
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getUnitValue(): ?float
    {
        return $this->unit_value;
    }

    /**
     * @param float|null $unit_value
     *
     * @return OptometryServiceItem
     */
    public function setUnitValue(?float $unit_value): OptometryServiceItem
    {
        $this->unit_value = $unit_value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAffectationIgvTypeId(): ?string
    {
        return $this->affectation_igv_type_id;
    }

    /**
     * @param string|null $affectation_igv_type_id
     *
     * @return OptometryServiceItem
     */
    public function setAffectationIgvTypeId(?string $affectation_igv_type_id): OptometryServiceItem
    {
        $this->affectation_igv_type_id = $affectation_igv_type_id;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalBaseIgv(): ?float
    {
        return $this->total_base_igv;
    }

    /**
     * @param float|null $total_base_igv
     *
     * @return OptometryServiceItem
     */
    public function setTotalBaseIgv(?float $total_base_igv): OptometryServiceItem
    {
        $this->total_base_igv = $total_base_igv;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPercentageIgv(): ?float
    {
        return $this->percentage_igv;
    }

    /**
     * @param float|null $percentage_igv
     *
     * @return OptometryServiceItem
     */
    public function setPercentageIgv(?float $percentage_igv): OptometryServiceItem
    {
        $this->percentage_igv = $percentage_igv;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalIgv(): ?float
    {
        return $this->total_igv;
    }

    /**
     * @param float|null $total_igv
     *
     * @return OptometryServiceItem
     */
    public function setTotalIgv(?float $total_igv): OptometryServiceItem
    {
        $this->total_igv = $total_igv;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSystemIscTypeId(): ?string
    {
        return $this->system_isc_type_id;
    }

    /**
     * @param string|null $system_isc_type_id
     *
     * @return OptometryServiceItem
     */
    public function setSystemIscTypeId(?string $system_isc_type_id): OptometryServiceItem
    {
        $this->system_isc_type_id = $system_isc_type_id;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalBaseIsc(): ?float
    {
        return $this->total_base_isc;
    }

    /**
     * @param float|null $total_base_isc
     *
     * @return OptometryServiceItem
     */
    public function setTotalBaseIsc(?float $total_base_isc): OptometryServiceItem
    {
        $this->total_base_isc = $total_base_isc;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPercentageIsc(): ?float
    {
        return $this->percentage_isc;
    }

    /**
     * @param float|null $percentage_isc
     *
     * @return OptometryServiceItem
     */
    public function setPercentageIsc(?float $percentage_isc): OptometryServiceItem
    {
        $this->percentage_isc = $percentage_isc;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalIsc(): ?float
    {
        return $this->total_isc;
    }

    /**
     * @param float|null $total_isc
     *
     * @return OptometryServiceItem
     */
    public function setTotalIsc(?float $total_isc): OptometryServiceItem
    {
        $this->total_isc = $total_isc;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalBaseOtherTaxes(): ?float
    {
        return $this->total_base_other_taxes;
    }

    /**
     * @param float|null $total_base_other_taxes
     *
     * @return OptometryServiceItem
     */
    public function setTotalBaseOtherTaxes(?float $total_base_other_taxes): OptometryServiceItem
    {
        $this->total_base_other_taxes = $total_base_other_taxes;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPercentageOtherTaxes(): ?float
    {
        return $this->percentage_other_taxes;
    }

    /**
     * @param float|null $percentage_other_taxes
     *
     * @return OptometryServiceItem
     */
    public function setPercentageOtherTaxes(?float $percentage_other_taxes): OptometryServiceItem
    {
        $this->percentage_other_taxes = $percentage_other_taxes;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalOtherTaxes(): ?float
    {
        return $this->total_other_taxes;
    }

    /**
     * @param float|null $total_other_taxes
     *
     * @return OptometryServiceItem
     */
    public function setTotalOtherTaxes(?float $total_other_taxes): OptometryServiceItem
    {
        $this->total_other_taxes = $total_other_taxes;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalPlasticBagTaxes(): ?float
    {
        return $this->total_plastic_bag_taxes;
    }

    /**
     * @param float|null $total_plastic_bag_taxes
     *
     * @return OptometryServiceItem
     */
    public function setTotalPlasticBagTaxes(?float $total_plastic_bag_taxes): OptometryServiceItem
    {
        $this->total_plastic_bag_taxes = $total_plastic_bag_taxes;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalTaxes(): ?float
    {
        return $this->total_taxes;
    }

    /**
     * @param float|null $total_taxes
     *
     * @return OptometryServiceItem
     */
    public function setTotalTaxes(?float $total_taxes): OptometryServiceItem
    {
        $this->total_taxes = $total_taxes;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPriceTypeId(): ?string
    {
        return $this->price_type_id;
    }

    /**
     * @param string|null $price_type_id
     *
     * @return OptometryServiceItem
     */
    public function setPriceTypeId(?string $price_type_id): OptometryServiceItem
    {
        $this->price_type_id = $price_type_id;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }

    /**
     * @param float|null $unit_price
     *
     * @return OptometryServiceItem
     */
    public function setUnitPrice(?float $unit_price): OptometryServiceItem
    {
        $this->unit_price = $unit_price;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalValue(): ?float
    {
        return $this->total_value;
    }

    /**
     * @param float|null $total_value
     *
     * @return OptometryServiceItem
     */
    public function setTotalValue(?float $total_value): OptometryServiceItem
    {
        $this->total_value = $total_value;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalCharge(): ?float
    {
        return $this->total_charge;
    }

    /**
     * @param float|null $total_charge
     *
     * @return OptometryServiceItem
     */
    public function setTotalCharge(?float $total_charge): OptometryServiceItem
    {
        $this->total_charge = $total_charge;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalDiscount(): ?float
    {
        return $this->total_discount;
    }

    /**
     * @param float|null $total_discount
     *
     * @return OptometryServiceItem
     */
    public function setTotalDiscount(?float $total_discount): OptometryServiceItem
    {
        $this->total_discount = $total_discount;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float|null $total
     *
     * @return OptometryServiceItem
     */
    public function setTotal(?float $total): OptometryServiceItem
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string|null
     */
    /*
        public function getAttributes(): ?string
        {
            return $this->attributes;
        }
        /*

        /**
         * @param string|null $attributes
         *
         * @return OptometryServiceItem
         */
    /*
        public function setAttributes(?string $attributes): OptometryServiceItem
        {
            $this->attributes = $attributes;
            return $this;
        }
        */

    /**
     * @return string|null
     */
    public function getDiscounts(): ?string
    {
        return $this->discounts;
    }

    /**
     * @param string|null $discounts
     *
     * @return OptometryServiceItem
     */
    public function setDiscounts(?string $discounts): OptometryServiceItem
    {
        $this->discounts = $discounts;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCharges(): ?string
    {
        return $this->charges;
    }

    /**
     * @param string|null $charges
     *
     * @return OptometryServiceItem
     */
    public function setCharges(?string $charges): OptometryServiceItem
    {
        $this->charges = $charges;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getWarehouseId(): ?int
    {
        return $this->warehouse_id;
    }

    /**
     * @param int|null $warehouse_id
     *
     * @return OptometryServiceItem
     */
    public function setWarehouseId(?int $warehouse_id): OptometryServiceItem
    {
        $this->warehouse_id = $warehouse_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameProductPdf(): ?string
    {
        return $this->name_product_pdf;
    }

    /**
     * @param string|null $name_product_pdf
     *
     * @return OptometryServiceItem
     */
    public function setNameProductPdf(?string $name_product_pdf): OptometryServiceItem
    {
        $this->name_product_pdf = $name_product_pdf;
        return $this;
    }


    public function getItemAttribute($value)
    {
        return ($value === null) ? null : (object)json_decode($value);
    }

    public function setItemAttribute($value)
    {
        $this->attributes['item'] = ($value === null) ? null : json_encode($value);
    }

    public function getAttributesAttribute($value)
    {
        return ($value === null) ? null : (object)json_decode($value);
    }

    public function setAttributesAttribute($value)
    {
        $this->attributes['attributes'] = ($value === null) ? null : json_encode($value);
    }

    public function getChargesAttribute($value)
    {
        return ($value === null) ? null : (object)json_decode($value);
    }

    public function setChargesAttribute($value)
    {
        $this->attributes['charges'] = ($value === null) ? null : json_encode($value);
    }

    public function getDiscountsAttribute($value)
    {
        return ($value === null) ? null : (object)json_decode($value);
    }

    public function setDiscountsAttribute($value)
    {
        $this->attributes['discounts'] = ($value === null) ? null : json_encode($value);
    }

    /**
     * @return BelongsTo
     */
    public function affectation_igv_type()
    {
        return $this->belongsTo(AffectationIgvType::class, 'affectation_igv_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function system_isc_type()
    {
        return $this->belongsTo(SystemIscType::class, 'system_isc_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function price_type()
    {
        return $this->belongsTo(PriceType::class, 'price_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function m_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * @return BelongsTo
     */
    public function technical_service()
    {
        return $this->belongsTo(OptometryService::class);
    }

    /**
     * @return BelongsTo
     */
    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * @return BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * @param $value
     *
     * @return false|string[]
     */
    public function getAdditionalInformationAttribute($value)
    {
        if (is_array($value)) $value = implode('|', $value);
        return explode('|', $value);
    }

    /**
     * @return array
     */
    public function getCollectionData()
    {
        $data = $this->toArray();
        return $data;
    }
}
