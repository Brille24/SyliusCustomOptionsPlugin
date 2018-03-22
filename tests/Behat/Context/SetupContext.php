<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context;


use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

class SetupContext implements Context
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CustomerOptionRepositoryInterface */
    private $customerOptionRepository;

    /** @var CustomerOptionGroupRepositoryInterface */
    private $customerOptionGroupRepository;

    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(
        EntityManagerInterface $em,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        ChannelContextInterface $channelContext,
        ChannelRepositoryInterface $channelRepository
    )
    {
        $this->em = $em;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->channelContext = $channelContext;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @Given I have a customer option named :name
     * @Given I have a customer option named :name in :locale
     * @Given I have a customer option named :name with type :type
     * @Given I have a customer option named :name in :locale with type :type
     */
    public function iHaveACustomerOptionNamed(string $name, string $locale = 'en_US', string $type = CustomerOptionTypeEnum::TEXT)
    {
        $code = str_replace([' '], '_', strtolower($name));

        $customerOption = new CustomerOption();
        $customerOption->setCode($code);
        $customerOption->setCurrentLocale($locale);
        $customerOption->setName($name);
        $customerOption->setType($type);

        $this->customerOptionRepository->add($customerOption);
    }


    /**
     * @Given customer option :customerOption has a value :valueName
     * @Given customer option :customerOption has a value named :valueName in :locale
     */
    public function customerOptionHasAValue(CustomerOptionInterface $customerOption, string $valueName, string $locale = 'en_US')
    {
        $value = new CustomerOptionValue();
        $value->setCode(strtolower(str_replace(' ', '_', $valueName)));
        $value->setCurrentLocale($locale);
        $value->setName($valueName);

        $customerOption->addValue($value);
    }

    /**
     * @Given customer option :customerOption has a value named :valueName in :locale priced :price
     * @Given customer option :customerOption has a value named :valueName in :locale priced :price in channel :channelName
     */
    public function customerOptionHasAValuePricedIn(CustomerOptionInterface $customerOption, string $valueName, string $locale, int $price, string $channelCode = 'WEB-US')
    {
        $value = new CustomerOptionValue();
        $value->setCode(strtolower(str_replace(' ', '_', $valueName)));
        $value->setCurrentLocale($locale);
        $value->setName($valueName);

        $valuePrice = new CustomerOptionValuePrice();
        $valuePrice->setCustomerOptionValue($value);

        $valuePrice->setType(CustomerOptionValuePrice::TYPE_FIXED_AMOUNT);
        $valuePrice->setAmount($price * 100);

        $valuePrice->setChannel($this->channelRepository->findOneByCode($channelCode));

        $value->addPrice($valuePrice);

        $customerOption->addValue($value);

        $this->em->persist($valuePrice);
        $this->em->persist($value);
        $this->em->flush();
    }

    /**
     * @Given I have a customer option group named :name
     * @Given I have a customer option group named :name in :locale
     */
    public function iHaveACustomerOptionGroupWithCodeNamed($name, $locale = 'en_US')
    {
        $code = str_replace([' '], '_', strtolower($name));

        $customerOptionGroup = new CustomerOptionGroup();
        $customerOptionGroup->setCode($code);
        $customerOptionGroup->setCurrentLocale($locale);
        $customerOptionGroup->setName($name);

        $this->customerOptionGroupRepository->add($customerOptionGroup);
    }

    /**
     * @Given customer option group :customerOptionGroup has option :customerOption
     */
    public function customerOptionGroupHasOption(
        CustomerOptionGroupInterface $customerOptionGroup,
        CustomerOptionInterface $customerOption
    )
    {
        $assoc = new CustomerOptionAssociation();

        $customerOptionGroup->addOptionAssociation($assoc);
        $customerOption->addGroupAssociation($assoc);
    }

    /**
     * @Given product :product has the customer option group :customerOptionGroup
     */
    public function productHasTheCustomerOptionGroup(
        ProductInterface $product,
        CustomerOptionGroupInterface $customerOptionGroup
    )
    {
        $product->setCustomerOptionGroup($customerOptionGroup);
        $customerOptionGroup->addProduct($product);

        $this->em->persist($product);
        $this->em->persist($customerOptionGroup);

        $this->em->flush();
    }

    /**
     * @Given customer option :customerOption is required
     */
    public function customerOptionIsRequired(CustomerOptionInterface $customerOption)
    {
        $customerOption->setRequired(true);

        $this->em->persist($customerOption);
        $this->em->flush();
    }

    /**
     * @Given customer option :customerOption is not required
     */
    public function customerOptionIsNotRequired(CustomerOptionInterface $customerOption)
    {
        $customerOption->setRequired(false);

        $this->em->persist($customerOption);
        $this->em->flush();
    }

    /**
     * @Given customer option :customerOption has constraint :min to :max
     */
    public function customerOptionHasConstraintValues(CustomerOptionInterface $customerOption, $min, $max)
    {
        $baseConfig = CustomerOptionTypeEnum::getConfigurationArray()[$customerOption->getType()];

        if(CustomerOptionTypeEnum::isDate($customerOption->getType())) {
            $min = new \DateTime($min);
            $max = new \DateTime($max);
        }else{
            $min = intval($min);
            $max = intval($max);
        }


        $config = [];
        foreach ($baseConfig as $key => $value){
            if(strpos($key, 'max') !== false){
                $config[$key] = $max;
            }elseif (strpos($key, 'min') !== false){
                $config[$key] = $min;
            }
        }

        $customerOption->setConfiguration($config);

        $this->em->persist($customerOption);
        $this->em->flush();
    }

    /**
     * @Given I chose value :value for option :customerOption for this order
     * @Given I entered value :value for option :customerOption for this order
     */
    public function iChoseValueForOptionForThisOrder(string $value, CustomerOptionInterface $customerOption)
    {
        $orderItems = $this->orderItemRepository->findAll();

        /** @var OrderItemInterface $orderItem */
        $orderItem = end($orderItems);

        Assert::notNull($orderItem);

        $values = [$value];

        if(CustomerOptionTypeEnum::isDate($customerOption->getType())){
            $date = new \DateTime($value);

            $values = [
                $date->format('d'),
                $date->format('m'),
                $date->format('Y'),
            ];
        }

        $config = $orderItem->getCustomerOptionConfiguration();

        foreach ($values as $value) {
            foreach ($config as $itemOption) {
                if ($itemOption->getCustomerOption() === $customerOption) {

                    if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
                        $customerOptionValue = $this->getCustomerOptionValueByName($customerOption, $value);

                        Assert::notNull($customerOptionValue);

                        $itemOption->setCustomerOptionValue($customerOptionValue);
                        $itemOption->setPrice($customerOptionValue->getPriceForChannel($this->channelContext->getChannel()));
                    } else {

                        if(empty($itemOption->getOptionValue())) {
                            $itemOption->setOptionValue($value);
                            break;
                        }
                    }

                }
            }
        }

        $orderItem->setCustomerOptionConfiguration($config);

        $this->em->persist($orderItem);
        $this->em->flush();
    }

    /**
     * @param CustomerOptionInterface $customerOption
     * @param string $name
     * @return CustomerOptionValueInterface|null
     */
    private function getCustomerOptionValueByName(CustomerOptionInterface $customerOption, string $name): ?CustomerOptionValueInterface
    {
        /** @var CustomerOptionValueInterface[] $customerOptionValues */
        $customerOptionValues = $customerOption->getValues();

        foreach ($customerOptionValues as $customerOptionValue){
            if($customerOptionValue->getName() === $name){
                return $customerOptionValue;
            }
        }

        return null;
    }
}
