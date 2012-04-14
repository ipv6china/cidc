<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Network
 *
 * @author y109
 */
class Etao_Common_SubNetwork
{

    protected $address;
    protected $addressString;
    protected $network;
    protected $networkString;
    protected $netmask;
    protected $netmaskString;
    protected $broadcast;
    protected $broadcastString;

    public function getBroadcast()
    {
        return $this->broadcast =  sprintf("%u", ip2long($this->getBroadcastString()));;
    }

    public function getAddress()
    {
        return sprintf("%u", ip2long($this->getAddressString()));
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddressString()
    {
        return $this->addressString;
    }

    public function setAddressString($addressString)
    {
        if(strpos($addressString, '/') > 0) {
            $ip_arr = explode('/', $addressString);
            $this->addressString = $ip_arr[0];
            $this->setAddress($this->setNetwork(sprintf("%u", ip2long($ip_arr[0]))));
            if(strlen($ip_arr[1]) > 2) {
                $num = self::mask2cidr($ip_arr[1]);
            } else {
                $num = $ip_arr[1];
            }
            $this->setNetmask($num);
        }
    }

    public static function mask2cidr($mask)
    {
        $long = ip2long($mask);
        $base = ip2long('255.255.255.255');
        return 32 - log(($long ^ $base) + 1, 2);
    }

    public function setBroadcast($broadcast)
    {
        $this->broadcast = $broadcast;
    }

    public function getBroadcastString()
    {
        $this->broadcastString = (long2ip(ip2long($this->getNetworkString())
                        | (~(ip2long($this->getNetmaskString())))));

        return $this->broadcastString;
    }

    public function setBroadcastString($broadcastString)
    {
        $this->broadcastString = $broadcastString;
    }

    public function getNetwork()
    {
        $this->network = sprintf("%u", ip2long($this->getNetworkString()));
        return $this->network;
    }

    public function setNetwork($network)
    {
        $this->network = $network;
    }

    public function getNetworkString()
    {
        $this->networkString = (long2ip((ip2long($this->getAddressString()))
                        & (ip2long($this->getNetmaskString()))));
        return $this->networkString;
    }

    public function setNetworkString($networkString)
    {
        $this->networkString = $networkString;
    }

    public function getNetmask()
    {
        return $this->netmask;
    }


    public function getNetmaskString()
    {
        return $this->netmaskString;
    }

    public function setNetmaskString($netmaskString)
    {
        $this->netmaskString = $netmaskString;
    }


    public function setNetmask($netmask)
    {
        $this->netmask = $netmask;
        $this->setNetmaskString(long2ip(ip2long("255.255.255.255") << (32 - $netmask)));
    }

    public function __construct()
    {

    }

    public function __toString()
    {
        $result = '';
        $result .= 'address:' . $this->getAddressString() . ' = ' . $this->getAddress() . "<br />";
        $result .= 'netmask:' . $this->getNetmaskString() . ' / ' . $this->getNetmask() . "<br />";
        $result .= 'network:' . $this->getNetworkString() . ' = ' . $this->getNetwork() . "<br />";
        $result .= 'broadca:' . $this->getBroadcastString() . ' = ' . $this->getBroadcast() . "<br />";
        return $result;
    }


}