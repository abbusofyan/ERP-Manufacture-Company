<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'info_type',
        'code',
        'country_id',
        'name',
        'country_phone_code_id',
        'phone',
        'uen',
        'account_manager',
        'customer_type',
        'service',
        'remark',
        'refrigeration_sales',
        'project',
        'address_location',
        'address_location_2',
        'address_unit_no',
        'address_building_name',
        'address_postal_code',
        'address_office_number',
        'poc_first_name',
        'poc_last_name',
        'poc_email',
        'poc_country_phone_code_id',
        'poc_phone',
        'poc_title',
        'credit_term',
        'credit_limit',
        'revenue',
    ];

    public function country_code(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'phone_code');
    }

    const TYPE_ORGANISATION = 1;
    const TYPE_INDIVIDUAL = 2;
    const TYPE_SALES_PERSON = 3;
    const TYPE_ARRAY = [
        self::TYPE_ORGANISATION => 'Organisation',
        self::TYPE_INDIVIDUAL => 'Individual',
        self::TYPE_SALES_PERSON => 'Salesperson',
    ];

    const MANAGER_1 = 1;
    const MANAGER_2 = 2;
    const MANAGER_3 = 3;
    const MANAGER_4 = 4;
    const MANAGER_ARRAY = [
        [
            'id' => self::MANAGER_1,
            'text' => 'Manager 1',
        ],
        [
            'id' => self::MANAGER_2,
            'text' => 'Manager 2',
        ],
        [
            'id' => self::MANAGER_3,
            'text' => 'Manager 3',
        ],
        [
            'id' => self::MANAGER_4,
            'text' => 'Manager 4',
        ],
    ];

    const TYPE_SERVICE_WORKSHOP = 1;
    const TYPE_MANUFACTURER = 2;
    const TYPE_INDIVIDUAL_OWNER = 3;
    const TYPE_FLEET_OWNER = 4;
    const TYPE_DISTRIBUTOR = 5;
    const CUSTOMER_TYPE_ARRAY = [
        [
            'id' => self::TYPE_SERVICE_WORKSHOP,
            'text' => 'Service Workshop',
        ],
        [
            'id' => self::TYPE_MANUFACTURER,
            'text' => 'Manufacturer',
        ],
        [
            'id' => self::TYPE_INDIVIDUAL_OWNER,
            'text' => 'Individual Owner',
        ],
        [
            'id' => self::TYPE_FLEET_OWNER,
            'text' => 'Fleet Owner',
        ],
        [
            'id' => self::TYPE_DISTRIBUTOR,
            'text' => 'Distributor',
        ],
    ];

    const SERVICE_1 = 1;
    const SERVICE_2 = 2;
    const SERVICE_3 = 3;
    const SERVICE_4 = 4;
    const SERVICE_ARRAY = [
        [
            'id' => self::SERVICE_1,
            'text' => 'Service 1',
        ],
        [
            'id' => self::SERVICE_2,
            'text' => 'Service 2',
        ],
        [
            'id' => self::SERVICE_3,
            'text' => 'Service 3',
        ],
        [
            'id' => self::SERVICE_4,
            'text' => 'Service 4',
        ],
    ];

    const REFRIGERATION_SALES_1 = 1;
    const REFRIGERATION_SALES_2 = 2;
    const REFRIGERATION_SALES_3 = 3;
    const REFRIGERATION_SALES_4 = 4;
    const REFRIGERATION_SALES_ARRAY = [
        [
            'id' => self::REFRIGERATION_SALES_1,
            'text' => 'Refrigeration Sales 1',
        ],
        [
            'id' => self::REFRIGERATION_SALES_2,
            'text' => 'Refrigeration Sales 2',
        ],
        [
            'id' => self::REFRIGERATION_SALES_3,
            'text' => 'Refrigeration Sales 3',
        ],
        [
            'id' => self::REFRIGERATION_SALES_4,
            'text' => 'Refrigeration Sales 4',
        ],
    ];

    const PROJECT_1 = 1;
    const PROJECT_2 = 2;
    const PROJECT_3 = 3;
    const PROJECT_4 = 4;
    const PROJECT_ARRAY = [
        [
            'id' => self::PROJECT_1,
            'text' => 'Project 1',
        ],
        [
            'id' => self::PROJECT_2,
            'text' => 'Project 2',
        ],
        [
            'id' => self::PROJECT_3,
            'text' => 'Project 3',
        ],
        [
            'id' => self::PROJECT_4,
            'text' => 'Project 4',
        ],
    ];

    const CREDIT_TERM_NET_DUE = "Net due";
    const CREDIT_TERM_7 = 7;
    const CREDIT_TERM_14 = 14;
    const CREDIT_TERM_30 = 30;
    const CREDIT_TERM_60 = 60;
    const CREDIT_TERM_90 = 90;
    const CREDIT_TERM_ARRAY = [
        [
            'id' => self::CREDIT_TERM_NET_DUE,
            'text' => 'NET DUE',
        ],
        [
            'id' => self::CREDIT_TERM_7,
            'text' => '7 Day(s)',
        ],
        [
            'id' => self::CREDIT_TERM_14,
            'text' => '14 Day(s)',
        ],
        [
            'id' => self::CREDIT_TERM_30,
            'text' => '30 Day(s)',
        ],
        [
            'id' => self::CREDIT_TERM_60,
            'text' => '60 Day(s)',
        ],
        [
            'id' => self::CREDIT_TERM_90,
            'text' => '90 Day(s)',
        ],
    ];
    const CURRENCIES = [
        [
            'id' => 'SGD',
            'text' => 'SGD',
        ],
        [
            'id' => 'USD',
            'text' => 'USD',
        ],
    ];

    protected $appends = [
        'account_manager_text',
        'customer_type_text',
        'service_text',
        'refrigeration_sales_text',
        'project_text',
        'credit_term_text',
    ];

    public function getAccountManagerTextAttribute()
    {
        return !empty($this->account_manager) ? $this->getById($this->account_manager, self::MANAGER_ARRAY) : null;
    }

    public function getCustomerTypeTextAttribute()
    {
        return !empty($this->customer_type) ? $this->getById($this->customer_type, self::CUSTOMER_TYPE_ARRAY) : null;
    }

    public function getServiceTextAttribute()
    {
        return !empty($this->service) ? $this->getById($this->service, self::SERVICE_ARRAY) : null;
    }

    public function getRefrigerationSalesTextAttribute()
    {
        return !empty($this->refrigeration_sales) ? $this->getById($this->refrigeration_sales, self::REFRIGERATION_SALES_ARRAY) : null;
    }

    public function getProjectTextAttribute()
    {
        return !empty($this->project) ? $this->getById($this->project, self::PROJECT_ARRAY) : null;
    }

    public function getCreditTermTextAttribute()
    {
        return !empty($this->credit_term) ? $this->getById($this->credit_term, self::CREDIT_TERM_ARRAY) : null;
    }

    private function getById($managerId, $array)
    {
        $manager = collect($array)->firstWhere('id', $managerId);

        return $manager['text'] ?? null;
    }


    /** Relationship **/
    public function countryPhoneCode(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_phone_code_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(CustomerBank::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(CustomerDiscount::class);
    }

    public function pocs(): HasMany
    {
        return $this->hasMany(Poc::class);
    }

    public function defaultPoc(): HasOne
    {
        return $this->hasOne(Poc::class)->where('is_default', 1);
    }


    public function updateRevenue($newRevenue)
    {
        // Calculate the old total revenue
        $oldRevenue = $this->revenue;

        // Update the total revenue
        $this->revenue = $oldRevenue + $newRevenue;

        // Save the updated customer revenue
        $this->save();
    }
}
