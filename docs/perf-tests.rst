Performance tests
=================

This kind of tests are effectiveley useless, i put theme here just in order to
understand frankie impacts.

My laptop (where i run this benchs)
-----------------------------------

.. code-block:: text

    Architecture:          x86_64
    CPU op-mode(s):        32-bit, 64-bit
    Byte Order:            Little Endian
    CPU(s):                4
    On-line CPU(s) list:   0-3
    Thread(s) per core:    2
    Core(s) per socket:    2
    Socket(s):             1
    NUMA node(s):          1
    Vendor ID:             GenuineIntel
    CPU family:            6
    Model:                 58
    Stepping:              9
    CPU MHz:               799.000
    BogoMIPS:              3392.48
    Virtualization:        VT-x
    L1d cache:             32K
    L1i cache:             32K
    L2 cache:              256K
    L3 cache:              3072K
    NUMA node0 CPU(s):     0-3

Apache - 2.4
------------

.. code-block:: text

    Server Software:        Apache/2.4.7
    Server Hostname:        frankie.127.0.0.1.xip.io
    Server Port:            80

    Document Path:          /
    Document Length:        22 bytes

    Concurrency Level:      5
    Time taken for tests:   242.864 seconds
    Complete requests:      100000
    Failed requests:        0
    Total transferred:      24100000 bytes
    HTML transferred:       2200000 bytes
    Requests per second:    411.75 [#/sec] (mean)
    Time per request:       12.143 [ms] (mean)
    Time per request:       2.429 [ms] (mean, across all concurrent requests)
    Transfer rate:          96.91 [Kbytes/sec] received

    Connection Times (ms)
                min  mean[+/-sd] median   max
    Connect:        0    0   0.1      0      13
    Processing:     6   12   4.3     10      64
    Waiting:        0   12   4.3     10      64
    Total:          6   12   4.3     10      64

    Percentage of the requests served within a certain time (ms)
    50%     10
    66%     12
    75%     14
    80%     15
    90%     18
    95%     21
    98%     24
    99%     27
    100%     64 (longest request)


HHVM - 3.5.1
------------

Here is the version of used during this test:

.. code-block:: text

    HipHop VM 3.5.1 (rel)
    Compiler: tags/HHVM-3.5.1-0-gb723e67c88bc008676687a04dff00db1faf250b3
    Repo schema: a395edf8708632a3d09f417dbd9b1bd1d5b50bbe


.. code-block:: text

    Server Software:        nginx/1.6.2
    Server Hostname:        localhost
    Server Port:            80

    Document Path:          /
    Document Length:        22 bytes

    Concurrency Level:      5
    Time taken for tests:   133.146 seconds
    Complete requests:      100000
    Failed requests:        0
    Total transferred:      20100000 bytes
    HTML transferred:       2200000 bytes
    Requests per second:    751.06 [#/sec] (mean)
    Time per request:       6.657 [ms] (mean)
    Time per request:       1.331 [ms] (mean, across all concurrent requests)
    Transfer rate:          147.42 [Kbytes/sec] received

    Connection Times (ms)
                min  mean[+/-sd] median   max
    Connect:        0    0   0.0      0       2
    Processing:     3    7   2.7      6      36
    Waiting:        3    6   2.7      6      36
    Total:          3    7   2.7      6      36

    Percentage of the requests served within a certain time (ms)
    50%      6
    66%      7
    75%      8
    80%      8
    90%     10
    95%     12
    98%     14
    99%     16
    100%     36 (longest request)

