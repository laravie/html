<?php

use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use Collective\Html\Eloquent\FormAccessible;

class FormAccessibleTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Model::unguard();

        $this->loadMigrationsFrom(__DIR__.'/migrations/');

        $this->now = Carbon::now();

        $this->modelData = [
          'string'  => 'abcdefghijklmnop',
          'email'   => 'tj@tjshafer.com',
          'address' => [
              'street' => 'abcde st',
          ],
          'array'         => [1, 2, 3],
          'transform_key' => 'testing testing',
          'created_at'    => $this->now,
          'updated_at'    => $this->now,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            \Collective\Html\HtmlServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Form' => \Collective\Html\FormFacade::class,
            'Html' => \Collective\Html\HtmlFacade::class,
        ];
    }

    public function testItCanMutateValuesForForms()
    {
        $model = new ModelThatUsesForms($this->modelData);
        Form::setModel($model);

        $this->assertEquals($model->getFormValue('string'), 'ponmlkjihgfedcba');
        $this->assertEquals($model->getFormValue('created_at'), $this->now->timestamp);
    }

    public function testItCanMutateRelatedValuesForForms()
    {
        $model                 = new ModelThatUsesForms($this->modelData);
        $relatedModel          = new ModelThatUsesForms($this->modelData);
        $relatedModel->address = [
            'street' => '123 Evergreen Terrace',
        ];
        $model->setRelation('related', $relatedModel);

        Form::setModel($model);

        $this->assertEquals(Form::getValueAttribute('related[string]'), 'ponmlkjihgfedcba');
        $this->assertEquals(Form::getValueAttribute('related[address][street]'), '123 Evergreen Terrace');
    }

    public function testItCanGetRelatedValueForForms()
    {
        $model = new ModelThatUsesForms($this->modelData);
        $this->assertEquals($model->getFormValue('address.street'), 'abcde st');
    }

    public function testItCanUseGetAccessorValuesWhenThereAreNoFormAccessors()
    {
        $model = new ModelThatUsesForms($this->modelData);
        Form::setModel($model);

        $this->assertEquals(Form::getValueAttribute('email'), 'mutated@tjshafer.com');
    }

    public function testItReturnsSameResultWithAndWithoutThisFeature()
    {
        $modelWithAccessor    = new ModelThatUsesForms($this->modelData);
        $modelWithoutAccessor = new ModelThatDoesntUseForms($this->modelData);

        Form::setModel($modelWithAccessor);
        $valuesWithAccessor[] = Form::getValueAttribute('array');
        $valuesWithAccessor[] = Form::getValueAttribute('array[0]');
        $valuesWithAccessor[] = Form::getValueAttribute('transform.key');
        Form::setModel($modelWithoutAccessor);
        $valuesWithoutAccessor[] = Form::getValueAttribute('array');
        $valuesWithoutAccessor[] = Form::getValueAttribute('array[0]');
        $valuesWithoutAccessor[] = Form::getValueAttribute('transform.key');

        $this->assertEquals($valuesWithAccessor, $valuesWithoutAccessor);
    }

    public function testItCanStillMutateValuesForViews()
    {
        $model = new ModelThatUsesForms($this->modelData);
        Form::setModel($model);

        $this->assertEquals($model->string, 'ABCDEFGHIJKLMNOP');
        $this->assertEquals($model->created_at, '1 second ago');
    }

    public function testItDoesntRequireTheUseOfThisFeature()
    {
        $model = new ModelThatDoesntUseForms($this->modelData);
        Form::setModel($model);

        $this->assertEquals($model->string, 'ABCDEFGHIJKLMNOP');
        $this->assertEquals($model->created_at, '1 second ago');
    }
}

class ModelThatUsesForms extends Model
{
    use FormAccessible;

    protected $table = 'models';

    public function formStringAttribute($value)
    {
        return strrev($value);
    }

    public function getStringAttribute($value)
    {
        return strtoupper($value);
    }

    public function formCreatedAtAttribute(Carbon $value)
    {
        return $value->timestamp;
    }

    public function getCreatedAtAttribute($value)
    {
        return '1 second ago';
    }

    public function getEmailAttribute($value)
    {
        return 'mutated@tjshafer.com';
    }
}

class ModelThatDoesntUseForms extends Model
{
    protected $table = 'models';

    public function getStringAttribute($value)
    {
        return strtoupper($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return '1 second ago';
    }
}
